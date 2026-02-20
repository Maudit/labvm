<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "protocol".
 *
 * @property int $id
 * @property string $name Nome
 * @property string|null $file_main_directory Directory contenitore per l'upload
 * @property string|null $file_sub_directory Directory contenente il file e le sue elaborazioni     
 * @property string|null $file_name Nome del file 
 * @property string|null $file_ext Estensione del file 
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Experiment[] $experiments
 */

class Protocol extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_UPLOAD_FILE = 'upload_image';
    const SCENARIO_DELETE_FILE = 'delete_image';

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'protocol';
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
            ],
            self::SCENARIO_UPDATE => [
                'name',
            ],
            self::SCENARIO_UPLOAD_FILE => [
                'file',
                'file_main_directory',
                'file_sub_directory',
                'file_name',
                'file_ext',
            ],
            self::SCENARIO_DELETE_FILE => [
                'file_main_directory',
                'file_sub_directory',
                'file_name',
                'file_ext',
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['file'], 'required', 'on' => [self::SCENARIO_UPLOAD_FILE]],
            [['name'], 'string', 'max' => 256],
            [['file_main_directory', 'file_ext'], 'string', 'max' => 4],
            [['file_sub_directory'], 'string', 'max' => 10],
            [['file_name'], 'string', 'max' => 255],
            //FILE EXT AND SIZE RULE
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf, doc, docx, xls, xlsx', 'maxSize' => 1024 * 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'file_main_directory' => 'Directory contenitore per l\'upload',
            'file_sub_directory' => 'Directory contenente il file e le sue elaborazioni     ',
            'file_name' => 'Nome del file ',
            'file_ext' => 'Estensione del file ',
            'created_at' => 'Creato il',
            'updated_at' => 'Modificato il',
            'created_by' => 'Creato da',
            'updated_by' => 'Modificato da',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    /**
     * Ritorna un array di protocolli nella forma id=>'name'
     */
    public static function getList()
    {
        return self::find()->select(['name'])->indexBy('id')->column();
    }

    public function saveFile()
    {
        try {
            $base_path = Yii::getAlias('@app/uploads/protocol/' . $this->file_main_directory . '/' . $this->file_sub_directory);
            $file_path = $base_path . '/' . $this->file_name . '.' . $this->file_ext;
            if (FileHelper::createDirectory($base_path)) {
                //Image::thumbnail($this->file->tempName, 300, null)->save($file_path, ['png_compression_level' => 3]);
                $this->file->saveAs($file_path);
                return true;
            }
        } catch (\Throwable $t) {
            return false;
        }
    }

    public function deleteFileFolder($base_path)
    {
        try {
            FileHelper::removeDirectory($base_path);
            return true;
        } catch (\Throwable $t) {
            return false;
        }
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
     * Gets query for [[Experiments]].
     *
     * @return \yii\db\ActiveQuery|ExperimentQuery
     */
    public function getExperiments()
    {
        return $this->hasMany(Experiment::class, ['protocol_id' => 'id']);
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
     * @return ProtocolQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProtocolQuery(get_called_class());
    }
}
