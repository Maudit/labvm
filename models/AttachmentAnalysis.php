<?php

namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "attachment_analysis".
 *
 * @property int $experiment_id
 * @property string $file_main_directory Directory contenitore per l'upload
 * @property string $file_sub_directory Directory contenente il file e le sue elaborazioni
 * @property string $file_name Nome del file
 * @property string $file_ext Estensione del file
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Experiment $experiment
 */
class AttachmentAnalysis extends \yii\db\ActiveRecord
{

    public $file;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attachment_analysis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['experiment_id', 'file_main_directory', 'file_sub_directory', 'file_name', 'file_ext'], 'required'],
            [['experiment_id'], 'integer'],
            [['file_main_directory', 'file_ext'], 'string', 'max' => 4],
            [['file_sub_directory'], 'string', 'max' => 10],
            [['file_name'], 'string', 'max' => 255],
            [['experiment_id'], 'unique'],
            [['experiment_id'], 'exist', 'skipOnError' => true, 'targetClass' => Experiment::class, 'targetAttribute' => ['experiment_id' => 'id']],
            //FILE EXT AND SIZE RULE
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf, doc, docx, xls, xlsx', 'maxSize' => 1024*1024*10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'experiment_id' => 'Experiment ID',
            'file_main_directory' => 'Directory contenitore per l\'upload',
            'file_sub_directory' => 'Directory contenente il file e le sue elaborazioni',
            'file_name' => 'Nome del file',
            'file_ext' => 'Estensione del file',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
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
     * Salva il file nella sua cartella.
     * @return bool|void 
     */
    public function saveFile()
    {
        try {
            $base_path = Yii::getAlias('@app/uploads/attachment-analysis/' . $this->file_main_directory . '/' . $this->file_sub_directory);
            $file_path = $base_path . '/' . $this->file_name . '.' . $this->file_ext;
            if (FileHelper::createDirectory($base_path)) {
                $this->file->saveAs($file_path);
                return true;
            }
        } catch (\Throwable $t) {
            return false;
        }
    }

    /**
     * Elimina il file rappresentato dal model.
     * @return bool 
     */
    public function deleteFile()
    {
        try {
            $fullPath = $this->getFullFilePath();
            return FileHelper::unlink($fullPath);
        } catch (\Throwable $t) {
            return false;
        }
    }

    /**
     * Elimina la cartella contenitore del file.
     * Nota: non è una funzione usata nel programma.
     * Richiede ulteriore implementazione con check 
     * sulla cartella: va eliminata solo se è vuota.
     * attualmente il programma elimina solo il file, 
     * lasciando la cartella nel file system anche se vuota.
     * @return bool 
     */
    public function deleteFileFolder()
    {
        try {
            $fullPath = $this->getFullDirectoryPath();
            FileHelper::removeDirectory($fullPath);
            return true;
        } catch (\Throwable $t) {
            return false;
        }
    }

    /**
     * Genera un nome di file per il download. 
     * Questo non è il nome che il file ha sul file system remoto, 
     * ma una composizione indicante il tipo di file (A)nalysis e gli 
     * id di (E)sperimento e (P)rotocollo.
     * 
     * @return string 
     */
    public function generateFileName()
    {
        $fileName = 'A';
        $fileName .= '#E' . $this->experiment->id;
        $fileName .= '-P' . $this->experiment->protocol->id;
        $fileName .= '.' . $this->file_ext;
        return $fileName;
    }

    /**
     * Ritorna il percorso assoluto della cartella contenente il file
     * 
     * @return string|false 
     * @throws InvalidArgumentException 
     */
    public function getFullDirectoryPath()
    {
        return Yii::getAlias('@app/uploads/attachment-analysis/' . $this->file_main_directory . '/' . $this->file_sub_directory);
    }

    /**
     * Ritorna il percorso assoluto del file
     * 
     * @return string 
     */
    public function getFullFilePath()
    {
        return $this->getFullDirectoryPath() . '/' . $this->file_name . '.' . $this->file_ext;
    }

    /**
     * Gets query for [[Experiment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExperiment()
    {
        return $this->hasOne(Experiment::class, ['id' => 'experiment_id']);
    }
}
