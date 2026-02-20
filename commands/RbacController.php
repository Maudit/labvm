<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        /**************************************************
         *                GESTIONE INVENTARIO             *
         **************************************************/

        //  add "deleteCompound" permission
        $deleteCompound = $auth->createPermission('deleteCompound');
        $deleteCompound->description = 'Elimina un composto dall\'inventario';
        $auth->add($deleteCompound);

        /**************************************************
         *                GESTIONE ESPERIMENTI            *
         **************************************************/

        // add "deleteExperiment" permission
        $deleteExperiment = $auth->createPermission('deleteExperiment');
        $deleteExperiment->description = 'Elimina un esperimento';
        $auth->add($deleteExperiment);

        // add "deleteTest" permission
        $deleteTest = $auth->createPermission('deleteTest');
        $deleteTest->description = 'Elimina un test di esperimento';
        $auth->add($deleteTest);

        /**************************************************
         *                GESTIONE OPZIONI                *
         **************************************************/

        // add "deleteManufacturer" permission
        $deleteManufacturer = $auth->createPermission('deleteManufacturer');
        $deleteManufacturer->description = 'Elimina un produttore';
        $auth->add($deleteManufacturer);

        // add "deleteProject" permission
        $deleteProject = $auth->createPermission('deleteProject');
        $deleteProject->description = 'Elimina un tag progetto';
        $auth->add($deleteProject); 

        // add "deleteProtocol" permission
        $deleteProtocol = $auth->createPermission('deleteProtocol');
        $deleteProtocol->description = 'Elimina un protocollo';
        $auth->add($deleteProtocol);

        // add "deleteLocation" permission
        $deleteLocation = $auth->createPermission('deleteLocation');
        $deleteLocation->description = 'Elimina una posizione';
        $auth->add($deleteLocation);

        /**************************************************
         *                GESTIONE UTENTI                 *
         **************************************************/
    
         // add "manageUsers" permission
        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Gestisce gli utenti';
        $auth->add($manageUsers);

        /**************************************************
         *                RUOLO E PERMESSI                *
         **************************************************/

        // Definisce e aggiunge il ruolo admin
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        // Assegna i permessi al ruolo admin
        $auth->addChild($admin, $deleteCompound);
        $auth->addChild($admin, $deleteExperiment);
        $auth->addChild($admin, $deleteTest);
        $auth->addChild($admin, $deleteManufacturer);
        $auth->addChild($admin, $deleteProject);
        $auth->addChild($admin, $deleteProtocol);
        $auth->addChild($admin, $deleteLocation);
        $auth->addChild($admin, $manageUsers);


        /**************************************************
         *              ASSEGNAZIONE RUOLO                *
         **************************************************/

         // 1 é l'id dell'utente Sandro Piludu
        $auth->assign($admin, 1);

    }
}
