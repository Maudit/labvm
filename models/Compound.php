<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use DateTime;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\db\Exception;
use yii\validators\DateValidator;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * This is the model class for table "compound".
 *
 * @property int $id
 * @property int $parent_id ID Genitore
 * @property string $name Nome
 * @property string $formula Formula molecolare
 * @property string|null $smiles SMILES
 * @property int $manufacturer_id Produttore
 * @property string $deposit_date Data di deposito
 * @property string|null $exhaustion_date Data di esaurimento
 * @property int $in_stock Disponibile
 * @property int $physical_form_id Forma fisica
 * @property int $location_id Posizione
 * @property string|null $notes Note
 * @property string|null $file_main_directory Directory contenitore per l'upload
 * @property string|null $file_sub_directory Directory contenente il file e le sue elaborazioni
 * @property string|null $file_name Nome del file
 * @property string|null $file_ext Estensione del file
 * @property int $created_at Creato il
 * @property int $updated_at Modificato il
 * @property int $created_by Creato da
 * @property int $updated_by Modificato da
 * @property int $deleted Eliminato
 * @property int $deleted_at Eliminato il
 * @property int $deleted_by Eliminato da
 *
 * @property Location $location
 * @property Test[] $tests
 */
class Compound extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DERIVE = 'derive';
    const SCENARIO_MARK_OUT = 'mark_out';
    const SCENARIO_MARK_IN = 'mark_in';
    const SCENARIO_UPLOAD_IMAGE = 'upload_image';
    const SCENARIO_DELETE_IMAGE = 'delete_image';
    //const SCENARIO_SOFT_DELETE = 'soft_delete';

    const STATUS_ALL = 'all';
    const STATUS_AVAILABLE = 1;
    const STATUS_EXHAUSTED = 0;
    

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'compound';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'Timestamp' => TimestampBehavior::class,
            'Blameable' => BlameableBehavior::class,
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
                'formula',
                'smiles',
                'manufacturer_id',
                'deposit_date',
                'physical_form_id',
                'location_id',
                'notes'
            ],
            self::SCENARIO_UPDATE => [
                'name',
                'formula',
                'smiles',
                'manufacturer_id',
                'deposit_date',
                'physical_form_id',
                'location_id',
                'notes'
            ],
            self::SCENARIO_DERIVE => [
                'deposit_date',
                'physical_form_id',
                'location_id',
                'notes'
            ],
            self::SCENARIO_UPLOAD_IMAGE => [
                'file',
                'file_main_directory',
                'file_sub_directory',
                'file_name',
                'file_ext',
            ],
            self::SCENARIO_DELETE_IMAGE => [
                'file_main_directory',
                'file_sub_directory',
                'file_name',
                'file_ext',
            ],
            self::SCENARIO_MARK_OUT => [
                'in_stock',
                'exhaustion_date',
            ],
            self::SCENARIO_MARK_IN => [
                'in_stock',
                'exhaustion_date',
            ],
            /*
            self::SCENARIO_SOFT_DELETE =>[
                'deleted',
                //'deleted_at',
                //'deleted_by'
            ]
            */
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //REQUIRED RULE
            [
                [
                    'name',
                    'formula',
                    'manufacturer_id',
                    'deposit_date',
                    'physical_form_id',
                    'location_id'
                ],
                'required',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_DERIVE],
            ],
            [
                [
                    'file',
                    'file_main_directory',
                    'file_sub_directory',
                    'file_name',
                    'file_ext'
                ], 'required', 'on' => [self::SCENARIO_UPLOAD_IMAGE,],
            ],


            //INTEGER RULE
            [['manufacturer_id', 'in_stock', 'physical_form_id', 'location_id'], 'integer'],

            //SAFE RULE
            [['deposit_date', 'exhaustion_date'], 'safe'],

            //STRING RULE
            [['notes'], 'string'],

            //STRING-LENGHT RULE
            [['name', 'formula'], 'string', 'max' => 255],
            [['smiles'], 'string', 'max' => 512],

            //EXIST RULE
            [['location_id'], 'exist', 'skipOnError' => true, 'targetClass' => Location::class, 'targetAttribute' => ['location_id' => 'id']],
            [['manufacturer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Manufacturer::class, 'targetAttribute' => ['manufacturer_id' => 'id']],

            //DATE VALIDATION RULE(S)
            [['deposit_date'], 'match', 'pattern' => '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i', 'message' => 'Errore nel formato della data. Pattern atteso: GG/MM/AAAA.', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_DERIVE]],
            [['deposit_date'], 'date', 'format' => 'dd/MM/yyyy', 'type' => DateValidator::TYPE_DATE, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE, self::SCENARIO_DERIVE]],

            [['exhaustion_date'], 'match', 'pattern' => '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/i', 'message' => 'Errore nel formato della data. Pattern atteso: GG/MM/AAAA.', 'on' => [self::SCENARIO_MARK_OUT]],
            [['exhaustion_date'], 'date', 'format' => 'dd/MM/yyyy', 'type' => DateValidator::TYPE_DATE, 'on' => [self::SCENARIO_MARK_OUT]],

            //FILE EXT AND SIZE RULE
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, webp', 'mimeTypes' => 'image/jpeg, image/png, image/webp', 'maxSize' => 1024 * 1000, 'on' => [self::SCENARIO_UPLOAD_IMAGE]],

            //FILE NAMING RULE(S)
            [['file_main_directory'], 'string', 'min' => 4, 'max' => 4, 'on' => [self::SCENARIO_UPLOAD_IMAGE]],
            [['file_sub_directory'], 'string', 'max' => 10, 'on' => [self::SCENARIO_UPLOAD_IMAGE]],
            [['file_name'], 'string', 'max' => 10, 'on' => [self::SCENARIO_UPLOAD_IMAGE]],
            [['file_ext'], 'string', 'max' => 4, 'on' => [self::SCENARIO_UPLOAD_IMAGE]],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->scenario == self::SCENARIO_CREATE) {

            //imposta lo stock come disponibile.
            $this->in_stock = 1;
            // Converte la data di deposito nel formato anno-mese-giorno, richiesto da MySQL per il campo data
            if ($this->deposit_date) {
                $date = DateTime::createFromFormat('d/m/Y', $this->deposit_date);
                $this->deposit_date = $date->format('Y-m-d');
            }
        }
        if ($this->scenario == self::SCENARIO_DERIVE) {

            //imposta lo stock come disponibile.
            $this->in_stock = 1;

            // Converte la data di deposito nel formato anno-mese-giorno, richiesto da MySQL per il campo data
            if ($this->deposit_date) {
                $date = DateTime::createFromFormat('d/m/Y', $this->deposit_date);
                $this->deposit_date = $date->format('Y-m-d');
            }
        }
        if ($this->scenario == self::SCENARIO_UPDATE) {

            // Converte la data di deposito nel formato anno-mese-giorno, richiesto da MySQL per il campo data
            if ($this->deposit_date) {
                $date = DateTime::createFromFormat('d/m/Y', $this->deposit_date);
                $this->deposit_date = $date->format('Y-m-d');
            }
        }

        if ($this->scenario == self::SCENARIO_MARK_OUT) {

            //imposta lo stock ad esaurito
            $this->in_stock = 0;

            // Converte la data di esaurimento nel formato anno-mese-giorno, richiesto da MySQL per il campo data
            if ($this->exhaustion_date) {
                $date = DateTime::createFromFormat('d/m/Y', $this->exhaustion_date);
                $this->exhaustion_date = $date->format('Y-m-d');
            }
        }

        if ($this->scenario == self::SCENARIO_MARK_IN) {

            //imposta lo stock ad esaurito
            $this->in_stock = 1;
            $this->exhaustion_date = null;
        }
        return parent::beforeSave($insert);
    }


    public function afterFind()
    {
        parent::afterFind();

        $action = Yii::$app->controller->action->id;

        if ($action === 'update') {
            // Converte la data di deposito nel formato giorno/mese/anno, usato nei widget per il campo data
            if ($this->deposit_date) {
                $date = DateTime::createFromFormat('Y-m-d', $this->deposit_date);
                $this->deposit_date = $date->format('d/m/Y');
            }
        }
    }

    public function saveImage()
    {
        try {
            $base_path = Yii::getAlias('@webroot/img/compounds/' . $this->file_main_directory . '/' . $this->file_sub_directory);
            $file_path = $base_path . '/' . $this->file_name . '.' . $this->file_ext;
            if (FileHelper::createDirectory($base_path)) {
                Image::thumbnail($this->file->tempName, 300, null)->save($file_path, ['png_compression_level' => 3]);
                return true;
            }
        } catch (\Throwable $t) {
            return false;
        }
    }

    public function deleteImageFolder($base_path)
    {
        try {
            FileHelper::removeDirectory($base_path);
            return true;
        } catch (\Throwable $t) {
            return false;
        }
    }

    /**
     * Verifica che l'immagine da cancellare sia esistente nel database e che abbia un ID valido.
     * @return bool 
     */
    public function hasImage()
    {
        if (
            is_numeric($this->file_main_directory) &&
            is_numeric($this->file_sub_directory) &&
            ($this->id == $this->file_sub_directory)
        ) {
            return true;
        }
        return false;
    }

    public function getImageUrl()
    {
        $imageUrl = Yii::getAlias('@web/img/compounds/') . 'default-300.png';
        if ($this->file_name) {
            $imageUrl = Yii::getAlias('@web/img/compounds/') . $this->file_main_directory . '/' . $this->file_sub_directory . '/' . $this->file_name . '.' . $this->file_ext;
        }
        return $imageUrl;
    }


    /**
     * Sposta il composto nel cestino
     * TODO: spostare anche i test degli esperimenti collegati al composto.
     * @return bool 
     * @throws InvalidConfigException 
     * @throws StaleObjectException 
     * @throws Exception 
     */
    /*
    public function trash()
    {
        $this->detachBehavior('Timestamp');
        $this->detachBehavior('Blameable');
        $this->deleted = 1;
        //$this->touch('deleted_at');
        $this->deleted_at = time();
        $this->deleted_by = Yii::$app->get('user')->id;
        if ($this->save())
        {
            return true;
        }
        return false;
    }

    public function restore()
    {
        $this->detachBehavior('Timestamp');
        $this->detachBehavior('Blameable');
        $this->deleted = 0;
        $this->deleted_at = null;
        $this->deleted_by = null;
        if ($this->save())
        {
            return true;
        }
        return false;
    }
    */

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' =>'IDG',
            'name' => 'Nome',
            'formula' => 'Peso molecolare',
            'smiles' => 'Stringa SMILES',
            'manufacturer_id' => 'Produttore',
            'deposit_date' => 'Data di deposito',
            'exhaustion_date' => 'Data di esaurimento',
            'in_stock' => 'Disponibilità',
            'physical_form_id' => 'Forma fisica',
            'location_id' => 'Posizione',
            'notes' => 'Note',
            'file_path' => 'Percorso del file',
            'file_name' => 'Nome del file',
            'file_ext' => 'Estensione del file',
            'file' => 'File immagine',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Modificato da',
        ];
    }

    /**
     * Gets query for [[Location]].
     *
     * @return \yii\db\ActiveQuery|LocationQuery
     */
    public function getLocation()
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    /**
     * Gets query for [[Manufacturer]].
     *
     * @return \yii\db\ActiveQuery|ManufacturerQuery
     */
    public function getManufacturer()
    {
        return $this->hasOne(Manufacturer::class, ['id' => 'manufacturer_id']);
    }

    /**
     * Gets query for [[Compound]].
     *
     * @return \yii\db\ActiveQuery|CompoundQuery
     */
    public function getParent()
    {
        return $this->hasOne(Compound::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Compound]].
     *
     * @return \yii\db\ActiveQuery|CompoundQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Compound::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Tests]].
     *
     * @return \yii\db\ActiveQuery|TestQuery
     */
    public function getTests()
    {
        return $this->hasMany(Test::class, ['compound_id' => 'id']);
    }


    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return CompoundQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CompoundQuery(get_called_class());
    }
}
