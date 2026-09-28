<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Contrôleurs généraux
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Responsable\AgentTrainingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Responsable\AgentAdvancementController;
use App\Http\Controllers\Responsable\AgentLeaveController;
use App\Http\Controllers\Responsable\AgentDisciplinaryActionController;
use App\Http\Controllers\Responsable\AgentRhDocumentController;
/*
|--------------------------------------------------------------------------
| Contrôleurs publics
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Public\AnnouncementController
    as PublicAnnouncementController;

use App\Http\Controllers\Public\TrackingController;

/*
|--------------------------------------------------------------------------
| Contrôleurs citoyens
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CitizenController
    as CitizenDashboardController;

use App\Http\Controllers\Citizen\ApplicationController
    as CitizenApplicationController;

use App\Http\Controllers\Citizen\OfficialDocumentController
    as CitizenOfficialDocumentController;

use App\Http\Controllers\Citizen\PaymentController
    as CitizenPaymentController;

/*
|--------------------------------------------------------------------------
| Contrôleurs agents publics
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Agent\ApplicationController
    as AgentApplicationController;

use App\Http\Controllers\Agent\DashboardController
    as AgentDashboardController;

use App\Http\Controllers\Agent\DocumentController
    as AgentDocumentController;

use App\Http\Controllers\Agent\OfficialDocumentController
    as AgentOfficialDocumentController;

/*
|--------------------------------------------------------------------------
| Contrôleurs responsables ministériels
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Responsable\AgentController
    as ResponsableAgentController;

use App\Http\Controllers\Responsable\DashboardController
    as ResponsableDashboardController;

use App\Http\Controllers\Responsable\RecruitmentAgentController
    as ResponsableRecruitmentAgentController;

/*
|--------------------------------------------------------------------------
| Contrôleurs administrateur national
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AgentController
    as AdminAgentController;

use App\Http\Controllers\Admin\AnnouncementController
    as AdminAnnouncementController;

use App\Http\Controllers\Admin\AuditController
    as AdminAuditController;

use App\Http\Controllers\Admin\CitizenController
    as AdminCitizenController;

use App\Http\Controllers\Admin\DashboardController
    as AdminDashboardController;

use App\Http\Controllers\Admin\DecisionDashboardController;

use App\Http\Controllers\Admin\MinistryController;

use App\Http\Controllers\Admin\NationalDashboardController;

use App\Http\Controllers\Admin\NationalReportController;

use App\Http\Controllers\Admin\ProcedureController;

use App\Http\Controllers\Admin\SearchController
    as AdminSearchController;

use App\Http\Controllers\Admin\SettingController
    as AdminSettingController;

use App\Http\Controllers\Admin\SystemHealthController;

use App\Http\Controllers\Admin\UserController;


/*
|--------------------------------------------------------------------------
| 1. PORTAIL PUBLIC
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Page d’accueil et informations publiques
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [PublicController::class, 'home']
)->name('home');

Route::get(
    '/services',
    [PublicController::class, 'services']
)->name('services');

Route::get(
    '/services/ministere/{ministry}',
    [PublicController::class, 'servicesByMinistry']
)->name('services.ministry');

Route::get(
    '/contact',
    [PublicController::class, 'contact']
)->name('contact');


/*
|--------------------------------------------------------------------------
| Suivi public d’une demande
|--------------------------------------------------------------------------
*/

Route::get(
    '/suivre-une-demande',
    [TrackingController::class, 'showForm']
)->name('public.tracking.form');

Route::post(
    '/suivre-une-demande',
    [TrackingController::class, 'search']
)->name('public.tracking.search');


/*
|--------------------------------------------------------------------------
| Annonces publiques
|--------------------------------------------------------------------------
*/

Route::get(
    '/annonces',
    [PublicAnnouncementController::class, 'index']
)->name('public.announcements.index');


/*
|--------------------------------------------------------------------------
| Vérification publique des documents officiels
|--------------------------------------------------------------------------
*/

Route::get(
    '/verification',
    [VerificationController::class, 'index']
)->name('verification.index');

Route::post(
    '/verification',
    [VerificationController::class, 'search']
)->name('verification.search');

Route::get(
    '/verification/resultat/{officialDocument}',
    [VerificationController::class, 'publicShow']
)->name('verification.documents.public');

Route::get(
    '/verification/document/{officialDocument}',
    [VerificationController::class, 'show']
)
    ->middleware('signed')
    ->name('verification.documents.show');


/*
|--------------------------------------------------------------------------
| 2. AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest')
    ->group(function (): void {

        Route::get(
            '/connexion',
            [AuthController::class, 'showLogin']
        )->name('login');

        Route::post(
            '/connexion',
            [AuthController::class, 'login']
        );

        Route::get(
            '/inscription',
            [AuthController::class, 'showRegister']
        )->name('register');

        Route::post(
            '/inscription',
            [AuthController::class, 'register']
        );
    });

Route::post(
    '/deconnexion',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

Route::get(
    '/dashboard',
    [AuthController::class, 'redirectDashboard']
)
    ->middleware('auth')
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| 3. NOTIFICATIONS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->group(function (): void {

        Route::get(
            '/notifications',
            [NotificationController::class, 'index']
        )->name('notifications.index');

        Route::patch(
            '/notifications/tout-lire',
            [NotificationController::class, 'readAll']
        )->name('notifications.read-all');

        Route::get(
            '/notifications/{notification}/ouvrir',
            [NotificationController::class, 'read']
        )->name('notifications.read');

        Route::delete(
            '/notifications/{notification}',
            [NotificationController::class, 'destroy']
        )->name('notifications.destroy');
    });


/*
|--------------------------------------------------------------------------
| 4. ESPACE CITOYEN
|--------------------------------------------------------------------------
*/

Route::prefix('citoyen')
    ->name('citizen.')
    ->middleware([
        'auth',
        'role:citoyen',
    ])
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Tableau de bord citoyen
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CitizenDashboardController::class, 'dashboard']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Demandes citoyennes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/demandes',
            [CitizenApplicationController::class, 'index']
        )->name('applications');

        Route::get(
            '/demande/nouvelle',
            [CitizenApplicationController::class, 'create']
        )->name('application.create');

        Route::post(
            '/demande',
            [CitizenApplicationController::class, 'store']
        )->name('application.store');

        /*
        |--------------------------------------------------------------------------
        | Paiements
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/paiements',
            [CitizenPaymentController::class, 'index']
        )->name('payments.index');

        Route::get(
            '/demandes/{application}/paiement',
            [CitizenPaymentController::class, 'create']
        )->name('payments.create');

        Route::post(
            '/demandes/{application}/paiement',
            [CitizenPaymentController::class, 'store']
        )->name('payments.store');

        Route::get(
            '/paiements/{payment}',
            [CitizenPaymentController::class, 'show']
        )->name('payments.show');

        Route::post(
            '/paiements/{payment}/confirmer',
            [CitizenPaymentController::class, 'confirm']
        )->name('payments.confirm');

        Route::post(
            '/paiements/{payment}/annuler',
            [CitizenPaymentController::class, 'cancel']
        )->name('payments.cancel');

        /*
        |--------------------------------------------------------------------------
        | Documents officiels
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/documents-officiels/{officialDocument}/telecharger',
            [CitizenOfficialDocumentController::class, 'download']
        )->name('official-documents.download');
    });


/*
|--------------------------------------------------------------------------
| 5. ESPACE AGENT PUBLIC
|--------------------------------------------------------------------------
|
| Ce groupe est désormais exclusivement réservé au rôle "agent".
| Le rôle "responsable" possède son propre espace indépendant.
|
*/

Route::prefix('agent')
    ->name('agent.')
    ->middleware([
        'auth',
        'role:agent',
    ])
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Tableau de bord agent
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AgentDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Traitement des demandes
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/demandes',
            [AgentApplicationController::class, 'index']
        )->name('applications');

        Route::get(
            '/demandes/{application}',
            [AgentApplicationController::class, 'show']
        )->name('applications.show');

        Route::match(
            [
                'post',
                'patch',
            ],
            '/demandes/{application}/statut',
            [AgentApplicationController::class, 'updateStatus']
        )->name('applications.status');

        /*
        |--------------------------------------------------------------------------
        | Documents déposés
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/documents/{document}/voir',
            [AgentDocumentController::class, 'show']
        )->name('documents.show');

        Route::get(
            '/documents/{document}/telecharger',
            [AgentDocumentController::class, 'download']
        )->name('documents.download');

        Route::patch(
            '/documents/{document}/statut',
            [AgentDocumentController::class, 'updateStatus']
        )->name('documents.status');

        /*
        |--------------------------------------------------------------------------
        | Documents officiels générés par l’agent
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/demandes/{application}/document-officiel',
            [AgentOfficialDocumentController::class, 'store']
        )->name('official-documents.store');

        Route::get(
            '/documents-officiels/{officialDocument}/telecharger',
            [AgentOfficialDocumentController::class, 'download']
        )->name('official-documents.download');
    });


/*
|--------------------------------------------------------------------------
| 6. ESPACE RESPONSABLE MINISTÉRIEL
|--------------------------------------------------------------------------
|
| Tous les responsables peuvent :
| - consulter leur supervision ministérielle ;
| - consulter les agents de leur ministère.
|
| Seul le responsable du ministère de la Fonction publique peut :
| - recruter un agent ;
| - affecter un agent ;
| - modifier son dossier ;
| - muter un agent ;
| - activer ou désactiver un compte ;
| - consulter l’historique administratif.
|
*/

Route::prefix('responsable')
    ->name('responsable.')
    ->middleware([
        'auth',
        'role:responsable',
    ])
    ->group(function (): void {



Route::get(
    '/agents/{agent}/formations',
    [AgentTrainingController::class, 'index']
)->name('agents.trainings.index');

Route::get(
    '/agents/{agent}/formations/create',
    [AgentTrainingController::class, 'create']
)->name('agents.trainings.create');

Route::post(
    '/agents/{agent}/formations',
    [AgentTrainingController::class, 'store']
)->name('agents.trainings.store');

Route::get(
    '/agents/{agent}/formations/{training}/certificat',
    [AgentTrainingController::class, 'downloadCertificate']
)->name('agents.trainings.certificate');
Route::get(
    '/agents/{agent}/documents-rh',
    [AgentRhDocumentController::class, 'index']
)->name('agents.documents.index');

Route::get(
    '/agents/{agent}/documents-rh/create',
    [AgentRhDocumentController::class, 'create']
)->name('agents.documents.create');

Route::post(
    '/agents/{agent}/documents-rh',
    [AgentRhDocumentController::class, 'store']
)->name('agents.documents.store');

Route::get(
    '/agents/{agent}/documents-rh/{document}/telecharger',
    [AgentRhDocumentController::class, 'download']
)->name('agents.documents.download');

Route::patch(
    '/agents/{agent}/documents-rh/{document}/archiver',
    [AgentRhDocumentController::class, 'archive']
)->name('agents.documents.archive');


Route::get(
    '/agents/{agent}/sanctions',
    [AgentDisciplinaryActionController::class, 'index']
)->name('agents.disciplinary.index');

Route::get(
    '/agents/{agent}/sanctions/create',
    [AgentDisciplinaryActionController::class, 'create']
)->name('agents.disciplinary.create');

Route::post(
    '/agents/{agent}/sanctions',
    [AgentDisciplinaryActionController::class, 'store']
)->name('agents.disciplinary.store');

Route::patch(
    '/agents/{agent}/sanctions/{disciplinary}/valider',
    [AgentDisciplinaryActionController::class, 'approve']
)->name('agents.disciplinary.approve');

Route::patch(
    '/agents/{agent}/sanctions/{disciplinary}/refuser',
    [AgentDisciplinaryActionController::class, 'reject']
)->name('agents.disciplinary.reject');
Route::get(
    '/agents/{agent}/conges',
    [AgentLeaveController::class, 'index']
)->name('agents.leaves.index');

Route::get(
    '/agents/{agent}/conges/create',
    [AgentLeaveController::class, 'create']
)->name('agents.leaves.create');

Route::post(
    '/agents/{agent}/conges',
    [AgentLeaveController::class, 'store']
)->name('agents.leaves.store');

Route::patch(
    '/agents/{agent}/conges/{leave}/valider',
    [AgentLeaveController::class, 'approve']
)->name('agents.leaves.approve');

Route::patch(
    '/agents/{agent}/conges/{leave}/refuser',
    [AgentLeaveController::class, 'reject']
)->name('agents.leaves.reject');
        /*
        |--------------------------------------------------------------------------
        | Tableau de bord responsable
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [ResponsableDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Consultation des agents
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/agents',
            [ResponsableAgentController::class, 'index']
        )->name('agents.index');
       Route::get(
           '/agents/{agent}/dossier-rh',
           [ResponsableAgentController::class, 'show']
       )->name('agents.show');
        /*
        |--------------------------------------------------------------------------
        | Cycle de gestion des agents publics
        |--------------------------------------------------------------------------
        |
        | Middleware supplémentaire :
        | responsable.fp
        |
        | Ce middleware vérifie que le responsable connecté dépend du
        | ministère ayant le code "FONCTION_PUBLIQUE".
        |
        */
		Route::get(
              '/agents/{agent}/avancement',
              [AgentAdvancementController::class, 'create']
              )->name('agents.advancement.create');

         Route::post(
               '/agents/{agent}/avancement',
                [AgentAdvancementController::class, 'store']
               )->name('agents.advancement.store');

        Route::prefix('recrutement')
            ->name('recruitment.')
            ->middleware('responsable.fp')
            ->group(function (): void {

                /*
                |--------------------------------------------------------------------------
                | Recrutement et affectation initiale
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/agents/create',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'create',
                    ]
                )->name('agents.create');

                Route::post(
                    '/agents',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'store',
                    ]
                )->name('agents.store');

                /*
                |--------------------------------------------------------------------------
                | Modification du dossier agent
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/agents/{agent}/edit',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'edit',
                    ]
                )->name('agents.edit');

                Route::put(
                    '/agents/{agent}',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'update',
                    ]
                )->name('agents.update');

                /*
                |--------------------------------------------------------------------------
                | Activation ou désactivation
                |--------------------------------------------------------------------------
                */

                Route::patch(
                    '/agents/{agent}/activation',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'toggle',
                    ]
                )->name('agents.toggle');

                /*
                |--------------------------------------------------------------------------
                | Mutation ministérielle
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/agents/{agent}/mutation',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'transferForm',
                    ]
                )->name('agents.transfer-form');

                Route::post(
                    '/agents/{agent}/mutation',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'transfer',
                    ]
                )->name('agents.transfer');

                /*
                |--------------------------------------------------------------------------
                | Historique administratif
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/agents/{agent}/historique',
                    [
                        ResponsableRecruitmentAgentController::class,
                        'history',
                    ]
                )->name('agents.history');
            });
    });


/*
|--------------------------------------------------------------------------
| 7. ADMINISTRATION NATIONALE
|--------------------------------------------------------------------------
|
| Toutes les routes contenues dans ce groupe sont réservées exclusivement
| au rôle "admin".
|
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware([
        'auth',
        'role:admin',
    ])
    ->group(function (): void {

        /*
        |--------------------------------------------------------------------------
        | Tableau de bord administrateur
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Pilotage décisionnel
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/pilotage-decisionnel',
            [DecisionDashboardController::class, 'index']
        )->name('decision-dashboard.index');

        /*
        |--------------------------------------------------------------------------
        | Recherche globale
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/recherche',
            [AdminSearchController::class, 'index']
        )->name('search.index');

        Route::get(
            '/demandes/{application}',
            [AdminSearchController::class, 'showApplication']
        )->name('applications.show');

        /*
        |--------------------------------------------------------------------------
        | Journal national et audit
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/journal',
            [AdminAuditController::class, 'index']
        )->name('audit.index');

        /*
        |--------------------------------------------------------------------------
        | Supervision nationale
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/supervision',
            [NationalDashboardController::class, 'index']
        )->name('supervision.index');

        Route::get(
            '/supervision/data',
            [NationalDashboardController::class, 'data']
        )->name('supervision.data');

        Route::get(
            '/supervision/rapport/pdf',
            [NationalReportController::class, 'pdf']
        )->name('supervision.report.pdf');

        Route::get(
            '/supervision/rapport/excel',
            [NationalReportController::class, 'excel']
        )->name('supervision.report.excel');

        /*
        |--------------------------------------------------------------------------
        | Citoyens
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/citoyens',
            [AdminCitizenController::class, 'index']
        )->name('citizens.index');

        Route::get(
            '/citoyens/{user}',
            [AdminCitizenController::class, 'show']
        )->name('citizens.show');

        Route::post(
            '/citoyens/{user}/toggle',
            [AdminCitizenController::class, 'toggle']
        )->name('citizens.toggle');

        /*
        |--------------------------------------------------------------------------
        | Agents publics
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/agents',
            [AdminAgentController::class, 'index']
        )->name('agents.index');

        Route::get(
            '/agents/create',
            [AdminAgentController::class, 'create']
        )->name('agents.create');

        Route::post(
            '/agents',
            [AdminAgentController::class, 'store']
        )->name('agents.store');

        Route::get(
            '/agents/{user}',
            [AdminAgentController::class, 'show']
        )->name('agents.show');

        Route::post(
            '/agents/{user}/toggle',
            [AdminAgentController::class, 'toggle']
        )->name('agents.toggle');

        /*
        |--------------------------------------------------------------------------
        | Gestion générale des utilisateurs
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'utilisateurs',
            UserController::class
        )
            ->parameters([
                'utilisateurs' => 'user',
            ])
            ->names('users');

        Route::patch(
            '/utilisateurs/{user}/activation',
            [UserController::class, 'toggle']
        )->name('users.toggle');

        Route::post(
            '/utilisateurs/{user}/mot-de-passe',
            [UserController::class, 'resetPassword']
        )->name('users.reset-password');

        /*
        |--------------------------------------------------------------------------
        | Ministères
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/ministeres/{ministry}/activation',
            [MinistryController::class, 'toggle']
        )->name('ministries.toggle');

        Route::resource(
            'ministeres',
            MinistryController::class
        )
            ->parameters([
                'ministeres' => 'ministry',
            ])
            ->names('ministries');

        /*
        |--------------------------------------------------------------------------
        | Démarches administratives
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/demarches/{procedure}/activation',
            [ProcedureController::class, 'toggle']
        )->name('procedures.toggle');

        Route::resource(
            'demarches',
            ProcedureController::class
        )
            ->parameters([
                'demarches' => 'procedure',
            ])
            ->names('procedures');

        /*
        |--------------------------------------------------------------------------
        | Annonces nationales
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/annonces',
            [AdminAnnouncementController::class, 'index']
        )->name('announcements.index');

        Route::get(
            '/annonces/create',
            [AdminAnnouncementController::class, 'create']
        )->name('announcements.create');

        Route::post(
            '/annonces',
            [AdminAnnouncementController::class, 'store']
        )->name('announcements.store');

        Route::get(
            '/annonces/{announcement}',
            [AdminAnnouncementController::class, 'show']
        )->name('announcements.show');

        Route::post(
            '/annonces/{announcement}/toggle',
            [AdminAnnouncementController::class, 'toggle']
        )->name('announcements.toggle');

        /*
        |--------------------------------------------------------------------------
        | Paramètres
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/parametres',
            [AdminSettingController::class, 'index']
        )->name('settings.index');

        Route::post(
            '/parametres',
            [AdminSettingController::class, 'update']
        )->name('settings.update');

        /*
        |--------------------------------------------------------------------------
        | Santé du système
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/sante-systeme',
            [SystemHealthController::class, 'index']
        )->name('system-health.index');
    });