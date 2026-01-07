<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationalPosition;
use App\Models\User;

class OrganizationalPositionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     * Auto-import structural data from hardcoded PublicController hierarchy
     */
    public function run(): void
    {
        $this->command->info('Importing struktural EMS data...');

        // Clear existing data
        OrganizationalPosition::truncate();

        // Helper function to find user by name (fuzzy matching)
        $findUser = function ($searchName) {
            if (empty($searchName) || $searchName === '[Belum diisi]') {
                return null;
            }

            // Clean search name
            $cleanName = $this->cleanName($searchName);

            // Try exact match first
            $user = User::where('is_active', true)
                ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
                ->first();

            if ($user)
                return $user->id;

            // Try contains match
            $user = User::where('is_active', true)
                ->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($cleanName) . '%'])
                ->first();

            if ($user)
                return $user->id;

            // Try first word match
            $words = explode(' ', $cleanName);
            if (!empty($words) && strlen($words[0]) > 2) {
                $user = User::where('is_active', true)
                    ->whereRaw('LOWER(name) LIKE ?', [strtolower($words[0]) . '%'])
                    ->first();

                if ($user)
                    return $user->id;
            }

            return null;
        };

        $order = 0;

        // LEVEL 0: HIGH COMMAND
        $this->command->info('⚡ Creating Level 0: High Command...');
        $ceo = OrganizationalPosition::create([
            'level' => 0,
            'level_key' => 'level_0',
            'title' => 'Chief Executive Officer (CEO)',
            'position_name' => 'High Command',
            'user_id' => $findUser('dr. Oliver Januari'),
            'display_order' => ++$order,
        ]);

        $director = OrganizationalPosition::create([
            'level' => 0,
            'level_key' => 'level_0',
            'title' => 'Hospital Director',
            'position_name' => ' High Command',
            'user_id' => $findUser('dr. Joseph Preistley'),
            'display_order' => ++$order,
        ]);

        $deputyDirector = OrganizationalPosition::create([
            'level' => 0,
            'level_key' => 'level_0',
            'title' => 'Deputy Director',
            'position_name' => 'High Command',
            'user_id' => $findUser('dr. Jehan L. Keenan'),
            'display_order' => ++$order,
        ]);

        // LEVEL 1: HUMAN CAPITAL DEPARTMENT
        $this->command->info('👥 Creating Level 1: Department of Human Capital...');
        $humanCapitalDept = OrganizationalPosition::create([
            'level' => 1,
            'level_key' => 'level_1',
            'parent_id' => $ceo->id,
            'title' => 'Department Head - Human Capital',
            'position_name' => 'Department of Human Capital',
            'user_id' => $findUser('dr. Oshee Khair'),
            'display_order' => ++$order,
        ]);

        // LEVEL 2: PEOPLE & DEVELOPMENT UNIT
        $this->command->info('📚 Creating Level 2: People & Development Unit...');
        $peopleDev = OrganizationalPosition::create([
            'level' => 2,
            'level_key' => 'level_2',
            'parent_id' => $humanCapitalDept->id,
            'title' => 'Head of People & Development',
            'position_name' => 'People & Development Unit',
            'user_id' => $findUser('dr. Kardus Smith'),
            'display_order' => ++$order,
        ]);

        // People & Development Staff
        $peopleDevStaff = [
            'dr. Chris Wynlee',
            'dr. Morgan Ackeric',
            'dr. Cecilia Wynlee',
            'Witel Ivy',
            'dr. Erga Shaka',
            'Udung Hayakawa',
            'Dilan Smith',
            'dr. Mike Weston',
            'dr. Nathan Ernesto'
        ];

        foreach ($peopleDevStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 2,
                'level_key' => 'level_2',
                'parent_id' => $peopleDev->id,
                'title' => 'Staff - People & Development',
                'position_name' => trim(str_replace(['dr.', 'S.Ked', ','], '', $staffName)),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // LEVEL 2: INDUSTRIAL & EMPLOYEE RELATION UNIT
        $this->command->info('⚙️ Creating Level 2: Industrial & Employee Relation Unit...');
        $industrialRel = OrganizationalPosition::create([
            'level' => 2,
            'level_key' => 'level_2',
            'parent_id' => $humanCapitalDept->id,
            'title' => 'Head of Industrial & Employee Relation',
            'position_name' => 'Industrial & Employee Relation Unit',
            'user_id' => $findUser('dr. Johns Ackeric'),
            'display_order' => ++$order,
        ]);

        OrganizationalPosition::create([
            'level' => 2,
            'level_key' => 'level_2',
            'parent_id' => $industrialRel->id,
            'title' => 'Deputy Head - Industrial & Employee Relation',
            'position_name' => 'Industrial & Employee Relation Unit',
            'user_id' => $findUser('dr. Lemi Ackeric'),
            'display_order' => ++$order,
        ]);

        $industrialStaff = [
            'dr. Mosawo Ackeric',
            'dr. Darren Ackeric',
            'Suep Rahman',
            'dr. Billy McCartney',
            'Nikola Charvi'
        ];

        foreach ($industrialStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 2,
                'level_key' => 'level_2',
                'parent_id' => $industrialRel->id,
                'title' => 'Staff - Industrial & Employee Relation',
                'position_name' => trim(str_replace(['dr.', 'S.Ked', ','], '', $staffName)),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // LEVEL 3: MEDICAL SCIENCE & LABORATORY
        $this->command->info('🔬 Creating Level 3: Department of Medical Science & Laboratory...');
        $medSciDept = OrganizationalPosition::create([
            'level' => 3,
            'level_key' => 'level_3',
            'parent_id' => $ceo->id,
            'title' => 'Department Head - Medical Science & Laboratory',
            'position_name' => 'Department of Medical Science & Laboratory',
            'user_id' => $findUser('dr. Aurelya L. Keenan'),
            'display_order' => ++$order,
        ]);

        // LEVEL 4: CLINICAL EDUCATION & LABORATORY
        $this->command->info('📖 Creating Level 4: Clinical Education & Laboratory...');
        $clinicalEdu = OrganizationalPosition::create([
            'level' => 4,
            'level_key' => 'level_4',
            'parent_id' => $medSciDept->id,
            'title' => 'Division Head - Clinical Education & Laboratory',
            'position_name' => 'Clinical Education & Laboratory Division',
            'user_id' => $findUser('dr. Edel C. Zion'),
            'display_order' => ++$order,
        ]);

        $clinicalLeads = ['dr. Tan Ackeric', 'dr. Achmad Djayadinigrat'];
        foreach ($clinicalLeads as $leadName) {
            OrganizationalPosition::create([
                'level' => 4,
                'level_key' => 'level_4',
                'parent_id' => $clinicalEdu->id,
                'title' => 'Lead - Clinical Education & Laboratory',
                'position_name' => trim(str_replace(['dr.', ','], '', $leadName)),
                'user_id' => $findUser($leadName),
                'display_order' => ++$order,
            ]);
        }

        $clinicalStaff = ['dr. Winnie A Honrado', 'Joel Aldridge'];
        foreach ($clinicalStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 4,
                'level_key' => 'level_4',
                'parent_id' => $clinicalEdu->id,
                'title' => 'Staff - Clinical Education & Laboratory',
                'position_name' => trim(str_replace(['dr.', 'S.Ked', ','], '', $staffName)),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // LEVEL 4: FORENSIC & MEDICO-LEGAL
        $this->command->info('⚖️ Creating Level 4: Forensic & Medico-Legal...');
        $forensic = OrganizationalPosition::create([
            'level' => 4,
            'level_key' => 'level_4',
            'parent_id' => $medSciDept->id,
            'title' => 'Division Head - Forensic & Medico-Legal',
            'position_name' => 'Forensic & Medico-Legal Laboratory Division',
            'user_id' => $findUser('dr. Winther Sham Weasley'),
            'display_order' => ++$order,
        ]);

        $forensicLeads = ['dr. Loen Sky', 'dr. Aiden Atmadja'];
        foreach ($forensicLeads as $leadName) {
            OrganizationalPosition::create([
                'level' => 4,
                'level_key' => 'level_4',
                'parent_id' => $forensic->id,
                'title' => 'Lead - Forensic & Medico-Legal',
                'position_name' => trim(str_replace(['dr.', ','], '', $leadName)),
                'user_id' => $findUser($leadName),
                'display_order' => ++$order,
            ]);
        }

        $forensicStaff = ['dr. Ray Aldridge', 'dr. Rindu Winfield'];
        foreach ($forensicStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 4,
                'level_key' => 'level_4',
                'parent_id' => $forensic->id,
                'title' => 'Staff - Forensic & Medico-Legal',
                'position_name' => trim(str_replace(['dr.', ','], '', $staffName)),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // LEVEL 5: GENERAL AFFAIR
        $this->command->info('🏢 Creating Level 5: Department of General Affair...');
        $generalAffair = OrganizationalPosition::create([
            'level' => 5,
            'level_key' => 'level_5',
            'parent_id' => $ceo->id,
            'title' => 'Department Head - General Affair',
            'position_name' => 'Department of General Affair',
            'user_id' => $findUser('drg. Abol Wangjanim'),
            'display_order' => ++$order,
        ]);

        OrganizationalPosition::create([
            'level' => 5,
            'level_key' => 'level_5',
            'parent_id' => $generalAffair->id,
            'title' => 'Deputy of General Affair',
            'position_name' => 'Department of General Affair',
            'user_id' => $findUser('dr. Haruu Ravenscroft'),
            'display_order' => ++$order,
        ]);

        // LEVEL 6: GENERAL AFFAIR DIVISIONS
        $this->command->info('📦 Creating Level 6: General Affair Divisions...');

        // Logistics Division
        $logistics = OrganizationalPosition::create([
            'level' => 6,
            'level_key' => 'level_6',
            'parent_id' => $generalAffair->id,
            'title' => 'Lead - Logistics Division',
            'position_name' => 'Logistics Division',
            'user_id' => $findUser('Wyda Cantik'),
            'display_order' => ++$order,
        ]);

        $logisticsStaff = ['Claw Navida', 'Queena Smith', 'Jatmiko Tjokronugroho'];
        foreach ($logisticsStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 6,
                'level_key' => 'level_6',
                'parent_id' => $logistics->id,
                'title' => 'Staff - Logistics',
                'position_name' => trim(str_replace(['S.Ked', 'A.Md. Kep', ','], '', $staffName)),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // Mobility Division
        $mobility = OrganizationalPosition::create([
            'level' => 6,
            'level_key' => 'level_6',
            'parent_id' => $generalAffair->id,
            'title' => 'Lead - Mobility Division',
            'position_name' => 'Mobility Division',
            'user_id' => $findUser('dr. Alicia L. Keenan'),
            'display_order' => ++$order,
        ]);

        OrganizationalPosition::create([
            'level' => 6,
            'level_key' => 'level_6',
            'parent_id' => $mobility->id,
            'title' => 'Assistant - Mobility Division',
            'position_name' => 'Mobility Division',
            'user_id' => $findUser('dr. Luffy Pielofi'),
            'display_order' => ++$order,
        ]);

        $mobilityStaff = [
            'Jamal Shakur',
            'Keenanyohooo Fukushima',
            'Rikuni Aldridge',
            'Ousmane Sulaiman',
            'Jibil Dossman',
            'Kim Hayakawa',
            'Hansaga Honrado',
            'Bjorn Buchigiri'
        ];

        foreach ($mobilityStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 6,
                'level_key' => 'level_6',
                'parent_id' => $mobility->id,
                'title' => 'Staff - Mobility (Dispatcher)',
                'position_name' => trim($staffName),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        // LEVEL 7: SPECIAL DIVISIONS
        $this->command->info('⚡ Creating Level 7: Special Divisions...');

        // Public Relation & Protocol
        $pr = OrganizationalPosition::create([
            'level' => 7,
            'level_key' => 'level_7',
            'parent_id' => $ceo->id,
            'title' => 'Head - Public Relation & Protocol',
            'position_name' => 'Public Relation & Protocol Division',
            'user_id' => $findUser('dr. Julian Rothschild'),
            'display_order' => ++$order,
        ]);

        // Disciplinary Committee
        $disciplinary = OrganizationalPosition::create([
            'level' => 7,
            'level_key' => 'level_7',
            'parent_id' => $ceo->id,
            'title' => 'Head - Disciplinary Committee',
            'position_name' => 'Disciplinary Committee',
            'user_id' => $findUser('dr. Valco Blanche'),
            'display_order' => ++$order,
        ]);

        $disciplinaryStaff = [
            'Emir Rothschild',
            'Mayura Atmadja',
            'Rashid Jamal Ackeric',
            'Yuki Hayakawa',
            'Ochi Atmadja',
            'Satryo Greenboys',
            'Lucas C Blanche'
        ];

        foreach ($disciplinaryStaff as $staffName) {
            OrganizationalPosition::create([
                'level' => 7,
                'level_key' => 'level_7',
                'parent_id' => $disciplinary->id,
                'title' => 'Member - Disciplinary Committee',
                'position_name' => trim($staffName),
                'user_id' => $findUser($staffName),
                'display_order' => ++$order,
            ]);
        }

        $total = OrganizationalPosition::count();
        $assigned = OrganizationalPosition::whereNotNull('user_id')->count();

        $this->command->info('');
        $this->command->info("✅ Import completed!");
        $this->command->info("📊 Total positions created: {$total}");
        $this->command->info("👤 Assigned to users: {$assigned}");
        $this->command->info("📭 Vacant positions: " . ($total - $assigned));
    }

    /**
     * Clean name from titles and extra characters
     */
    private function cleanName($name)
    {
        // Remove common titles and suffixes
        $name = str_replace(['dr.', 'Dr.', 'drg.', 'Drg.', 'Sp.', 'M.Sos.', 'S.Ked', 'A.Md. Kep.'], '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}
