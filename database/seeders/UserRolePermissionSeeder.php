<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        Permission::create([
            'name' => 'view roles',
            'display_name' => 'Voir les rôles',
            'description' => 'Permet à un utilisateur de consulter la liste des rôles et leurs détails au sein de l\'application.',
            'slug' => Str::random(10),
        ]);
        // Permissions pour "role"
Permission::create([
    'name' => 'create role',
    'display_name' => 'Créer un rôle',
    'description' => 'Permet à un utilisateur de créer un nouveau rôle dans l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update role',
    'display_name' => 'Modifier un rôle',
    'description' => 'Permet à un utilisateur de modifier les détails d\'un rôle existant.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete role',
    'display_name' => 'Supprimer un rôle',
    'description' => 'Permet à un utilisateur de supprimer un rôle de l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view role',
    'display_name' => 'Voir les rôles',
    'description' => 'Permet à un utilisateur de consulter la liste des rôles et leurs détails.',
    'slug' => Str::random(10),
]);

// Permissions pour "permission"
Permission::create([
    'name' => 'create permission',
    'display_name' => 'Créer une permission',
    'description' => 'Permet à un utilisateur de créer une nouvelle permission dans l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update permission',
    'display_name' => 'Modifier une permission',
    'description' => 'Permet à un utilisateur de modifier une permission existante.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete permission',
    'display_name' => 'Supprimer une permission',
    'description' => 'Permet à un utilisateur de supprimer une permission de l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view permission',
    'display_name' => 'Voir les permissions',
    'description' => 'Permet à un utilisateur de consulter la liste des permissions et leurs détails.',
    'slug' => Str::random(10),
]);

// Permissions pour "user"
Permission::create([
    'name' => 'create user',
    'display_name' => 'Créer un utilisateur',
    'description' => 'Permet à un utilisateur de créer un nouveau compte utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update user',
    'display_name' => 'Modifier un utilisateur',
    'description' => 'Permet à un utilisateur de modifier les informations d\'un compte utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete user',
    'display_name' => 'Supprimer un utilisateur',
    'description' => 'Permet à un utilisateur de supprimer un compte utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view user',
    'display_name' => 'Voir les utilisateurs',
    'description' => 'Permet à un utilisateur de consulter la liste des utilisateurs.',
    'slug' => Str::random(10),
]);

// Permissions pour "classe"
Permission::create([
    'name' => 'create classe',
    'display_name' => 'Créer une classe',
    'description' => 'Permet à un utilisateur de créer une nouvelle classe dans l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update classe',
    'display_name' => 'Modifier une classe',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une classe.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete classe',
    'display_name' => 'Supprimer une classe',
    'description' => 'Permet à un utilisateur de supprimer une classe.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view classe',
    'display_name' => 'Voir les classes',
    'description' => 'Permet à un utilisateur de consulter la liste des classes.',
    'slug' => Str::random(10),
]);

// Permissions pour "matière"
Permission::create([
    'name' => 'create matiere',
    'display_name' => 'Créer une matière',
    'description' => 'Permet à un utilisateur de créer une nouvelle matière.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update matiere',
    'display_name' => 'Modifier une matière',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une matière.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete matiere',
    'display_name' => 'Supprimer une matière',
    'description' => 'Permet à un utilisateur de supprimer une matière.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view matiere',
    'display_name' => 'Voir les matières',
    'description' => 'Permet à un utilisateur de consulter la liste des matières.',
    'slug' => Str::random(10),
]);

// Permissions pour "matière de la classe"
Permission::create([
    'name' => 'create matiereDeLaclasse',
    'display_name' => 'Créer une matière de classe',
    'description' => 'Permet à un utilisateur de créer une matière pour une classe spécifique.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update matiereDeLaclasse',
    'display_name' => 'Modifier une matière de classe',
    'description' => 'Permet à un utilisateur de modifier une matière assignée à une classe.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete matiereDeLaclasse',
    'display_name' => 'Supprimer une matière de classe',
    'description' => 'Permet à un utilisateur de supprimer une matière d\'une classe.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view matiereDeLaclasse',
    'display_name' => 'Voir les matières de classe',
    'description' => 'Permet à un utilisateur de consulter les matières assignées à une classe.',
    'slug' => Str::random(10),
]);

// Permissions pour "période"
Permission::create([
    'name' => 'create periode',
    'display_name' => 'Créer une période',
    'description' => 'Permet à un utilisateur de créer une nouvelle période.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update periode',
    'display_name' => 'Modifier une période',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une période.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete periode',
    'display_name' => 'Supprimer une période',
    'description' => 'Permet à un utilisateur de supprimer une période.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view periode',
    'display_name' => 'Voir les périodes',
    'description' => 'Permet à un utilisateur de consulter la liste des périodes.',
    'slug' => Str::random(10),
]);

// Permissions pour "chapitre"
Permission::create([
    'name' => 'create chapitre',
    'display_name' => 'Créer un chapitre',
    'description' => 'Permet à un utilisateur de créer un nouveau chapitre.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update chapitre',
    'display_name' => 'Modifier un chapitre',
    'description' => 'Permet à un utilisateur de modifier les détails d\'un chapitre.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete chapitre',
    'display_name' => 'Supprimer un chapitre',
    'description' => 'Permet à un utilisateur de supprimer un chapitre.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view chapitre',
    'display_name' => 'Voir les chapitres',
    'description' => 'Permet à un utilisateur de consulter la liste des chapitres.',
    'slug' => Str::random(10),
]);

// Permissions pour "leçon"
Permission::create([
    'name' => 'create lecon',
    'display_name' => 'Créer une leçon',
    'description' => 'Permet à un utilisateur de créer une nouvelle leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update lecon',
    'display_name' => 'Modifier une leçon',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete lecon',
    'display_name' => 'Supprimer une leçon',
    'description' => 'Permet à un utilisateur de supprimer une leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view lecon',
    'display_name' => 'Voir les leçons',
    'description' => 'Permet à un utilisateur de consulter la liste des leçons.',
    'slug' => Str::random(10),
]);

// Permissions pour "cours"
Permission::create([
    'name' => 'create cours',
    'display_name' => 'Créer un cours',
    'description' => 'Permet à un utilisateur de créer un nouveau cours.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update cours',
    'display_name' => 'Modifier un cours',
    'description' => 'Permet à un utilisateur de modifier les détails d\'un cours.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete cours',
    'display_name' => 'Supprimer un cours',
    'description' => 'Permet à un utilisateur de supprimer un cours.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view cours',
    'display_name' => 'Voir les cours',
    'description' => 'Permet à un utilisateur de consulter la liste des cours.',
    'slug' => Str::random(10),
]);

// Permissions pour "évaluation de leçon"
Permission::create([
    'name' => 'create evaluationLecon',
    'display_name' => 'Créer une évaluation de leçon',
    'description' => 'Permet à un utilisateur de créer une évaluation pour une leçon spécifique.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update evaluationLecon',
    'display_name' => 'Modifier une évaluation de leçon',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une évaluation de leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete evaluationLecon',
    'display_name' => 'Supprimer une évaluation de leçon',
    'description' => 'Permet à un utilisateur de supprimer une évaluation de leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view evaluationLecon',
    'display_name' => 'Voir les évaluations de leçon',
    'description' => 'Permet à un utilisateur de consulter les évaluations de leçon.',
    'slug' => Str::random(10),
]);

// Permissions pour "évaluation"
Permission::create([
    'name' => 'create evaluation',
    'display_name' => 'Créer une évaluation',
    'description' => 'Permet à un utilisateur de créer une nouvelle évaluation.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update evaluation',
    'display_name' => 'Modifier une évaluation',
    'description' => 'Permet à un utilisateur de modifier les détails d\'une évaluation.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete evaluation',
    'display_name' => 'Supprimer une évaluation',
    'description' => 'Permet à un utilisateur de supprimer une évaluation.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view evaluation',
    'display_name' => 'Voir les évaluations',
    'description' => 'Permet à un utilisateur de consulter la liste des évaluations.',
    'slug' => Str::random(10),
]);

// Permissions pour "discussion"
Permission::create([
    'name' => 'create discussion',
    'display_name' => 'Créer une discussion',
    'description' => 'Permet à un utilisateur de créer une nouvelle discussion.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update discussion',
    'display_name' => 'Modifier une discussion',
    'description' => 'Permet à un utilisateur de modifier une discussion existante.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete discussion',
    'display_name' => 'Supprimer une discussion',
    'description' => 'Permet à un utilisateur de supprimer une discussion.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view discussion',
    'display_name' => 'Voir les discussions',
    'description' => 'Permet à un utilisateur de consulter la liste des discussions.',
    'slug' => Str::random(10),
]);

// Permissions pour "classeVirtuelle"
Permission::create([
    'name' => 'create classeVirtuelle',
    'display_name' => 'Créer une classe virtuelle',
    'description' => 'Permet à un utilisateur de créer une nouvelle classe virtuelle.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update classeVirtuelle',
    'display_name' => 'Modifier une classe virtuelle',
    'description' => 'Permet à un utilisateur de modifier une classe virtuelle.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete classeVirtuelle',
    'display_name' => 'Supprimer une classe virtuelle',
    'description' => 'Permet à un utilisateur de supprimer une classe virtuelle.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view classeVirtuelle',
    'display_name' => 'Voir les classes virtuelles',
    'description' => 'Permet à un utilisateur de consulter la liste des classes virtuelles.',
    'slug' => Str::random(10),
]);

// Permissions pour "eleve"
Permission::create([
    'name' => 'create eleve',
    'display_name' => 'Créer un élève',
    'description' => 'Permet à un utilisateur de créer un élève.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update eleve',
    'display_name' => 'Modifier un élève',
    'description' => 'Permet à un utilisateur de modifier les informations d\'un élève.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete eleve',
    'display_name' => 'Supprimer un élève',
    'description' => 'Permet à un utilisateur de supprimer un élève.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view eleve',
    'display_name' => 'Voir les élèves',
    'description' => 'Permet à un utilisateur de consulter la liste des élèves.',
    'slug' => Str::random(10),
]);

// Permissions pour "enseignant"
Permission::create([
    'name' => 'create enseignant',
    'display_name' => 'Créer un enseignant',
    'description' => 'Permet à un utilisateur de créer un enseignant.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update enseignant',
    'display_name' => 'Modifier un enseignant',
    'description' => 'Permet à un utilisateur de modifier les informations d\'un enseignant.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete enseignant',
    'display_name' => 'Supprimer un enseignant',
    'description' => 'Permet à un utilisateur de supprimer un enseignant.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view enseignant',
    'display_name' => 'Voir les enseignants',
    'description' => 'Permet à un utilisateur de consulter la liste des enseignants.',
    'slug' => Str::random(10),
]);

// Permissions pour "parent"
Permission::create([
    'name' => 'create parent',
    'display_name' => 'Créer un parent',
    'description' => 'Permet à un utilisateur de créer un parent.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update parent',
    'display_name' => 'Modifier un parent',
    'description' => 'Permet à un utilisateur de modifier les informations d\'un parent.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete parent',
    'display_name' => 'Supprimer un parent',
    'description' => 'Permet à un utilisateur de supprimer un parent.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view parent',
    'display_name' => 'Voir les parents',
    'description' => 'Permet à un utilisateur de consulter la liste des parents.',
    'slug' => Str::random(10),
]);

// Permissions pour "utilisateur"
Permission::create([
    'name' => 'create utilisateur',
    'display_name' => 'Créer un utilisateur',
    'description' => 'Permet à un utilisateur de créer un utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update utilisateur',
    'display_name' => 'Modifier un utilisateur',
    'description' => 'Permet à un utilisateur de modifier les informations d\'un utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete utilisateur',
    'display_name' => 'Supprimer un utilisateur',
    'description' => 'Permet à un utilisateur de supprimer un utilisateur.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view utilisateur',
    'display_name' => 'Voir les utilisateurs',
    'description' => 'Permet à un utilisateur de consulter la liste des utilisateurs.',
    'slug' => Str::random(10),
]);

// Permissions pour "parametre"
Permission::create([
    'name' => 'update parametre',
    'display_name' => 'Modifier les paramètres',
    'description' => 'Permet à un utilisateur de modifier les paramètres de l\'application.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view parametre',
    'display_name' => 'Voir les paramètres',
    'description' => 'Permet à un utilisateur de consulter les paramètres de l\'application.',
    'slug' => Str::random(10),
]);

// Permissions pour "questionLecon"
Permission::create([
    'name' => 'create questionLecon',
    'display_name' => 'Créer une question de leçon',
    'description' => 'Permet à un utilisateur de créer une nouvelle question pour une leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update questionLecon',
    'display_name' => 'Modifier une question de leçon',
    'description' => 'Permet à un utilisateur de modifier une question existante pour une leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete questionLecon',
    'display_name' => 'Supprimer une question de leçon',
    'description' => 'Permet à un utilisateur de supprimer une question liée à une leçon.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view questionLecon',
    'display_name' => 'Voir les questions de leçon',
    'description' => 'Permet à un utilisateur de consulter la liste des questions pour les leçons.',
    'slug' => Str::random(10),
]);

// Permissions pour "question"
Permission::create([
    'name' => 'create question',
    'display_name' => 'Créer une question',
    'description' => 'Permet à un utilisateur de créer une nouvelle question.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'update question',
    'display_name' => 'Modifier une question',
    'description' => 'Permet à un utilisateur de modifier une question existante.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'delete question',
    'display_name' => 'Supprimer une question',
    'description' => 'Permet à un utilisateur de supprimer une question.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'view question',
    'display_name' => 'Voir les questions',
    'description' => 'Permet à un utilisateur de consulter la liste des questions.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'assign role',
    'display_name' => 'Attribuer un groupe',
    'description' => 'Permet à un utilisateur d\'attribuer un groupe à un utilisateur ou entité.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'assign permission',
    'display_name' => 'Attribuer un droit',
    'description' => 'Permet à un utilisateur d\'attribuer un droit spécifique à un utilisateur ou entité.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'assign classe',
    'display_name' => 'Attribuer une classe',
    'description' => 'Permet à un utilisateur d\'attribuer une classe à un élève ou enseignant.',
    'slug' => Str::random(10),
]);

Permission::create([
    'name' => 'assign matiere',
    'display_name' => 'Attribuer une matière',
    'description' => 'Permet à un utilisateur d\'attribuer une matière à un enseignant ou élève.',
    'slug' => Str::random(10),
]);




        // Create Roles
        //$superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Administrateur',
            'description' => 'A accès à toutes les fonctionnalités du système sans aucune restriction.',
            'slug' => Str::random(10),
        ]);

        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrateur',
            'description' => 'Peut gérer les utilisateurs et les paramètres de l\'application.',
            'slug' => Str::random(10),
        ]);

        Role::create([
            'name' => 'moderator',
            'display_name' => 'Modérateur',
            'description' => 'Peut modérer le contenu et gérer certains aspects de la communauté.',
            'slug' => Str::random(10),
        ]);

        Role::create([
            'name' => 'teacher',
            'display_name' => 'Enseignant',
            'description' => 'Peut créer et gérer les cours, les leçons et les évaluations pour les étudiants.',
            'slug' => Str::random(10),
        ]);

        Role::create([
            'name' => 'student',
            'display_name' => 'Étudiant',
            'description' => 'Accès aux cours, leçons et évaluations créés par les enseignants.',
            'slug' => Str::random(10),
        ]);

        Role::create([
            'name' => 'guest',
            'display_name' => 'Invité',
            'description' => 'Accès limité à certaines parties du contenu public de la plateforme.',
            'slug' => Str::random(10),
        ]);


        // Lets give all permission to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();

        $superAdminRole->givePermissionTo($allPermissionNames);


        // Let's Create User and assign Role to it.

        $superAdminUser = User::firstOrCreate([
                    'email' => 'super.admin@enumera.tech',
                ], [
                    'nom' => 'Super',
                    'prenom' => 'Admin',
                    'date_de_naissance' => '1990-01-01',
                    'genre' => 'M',
                    'profile' => 'ADMIN',
                    'telephone' => '70000000',
                    'matricule' => '70000000',
                    'slug' => Str::random(10),
                    'isActive' => true,
                    'email' => 'super.admin@enumera.tech',
                    'password' => Hash::make ('12345678'),
                ]);

        $superAdminUser->assignRole($superAdminRole);
        $superAdminUser->syncPermissions($allPermissionNames);


    }
}
