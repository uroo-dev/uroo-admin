<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use App\Models\AppIdea;
use App\Models\Bookmark;
use App\Models\BrainDump;
use App\Models\ChecklistItem;
use App\Models\Client;
use App\Models\Commit;
use App\Models\Credential;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\Project;
use App\Models\QualityChecklist;
use App\Models\Repository;
use App\Models\SavingsGoal;
use App\Models\SavingsTransaction;
use App\Models\Subscription;

class DemoSeeder extends Seeder
{
    public int $userId = 1;

    public function run(): void
    {
        $this->seedGitHub();
        $this->seedCredentials();
        $this->seedClients();
        $this->seedInvoices();
        $this->seedProjects();
        $this->seedNotes();
        $this->seedBookmarks();
        $this->seedQualityControl();
        $this->seedIdeas();
        $this->seedBrainDump();
        $this->seedSavings();
        $this->seedSubscriptions();
    }

    protected function seedGitHub(): void
    {
        $repo = Repository::create([
            'user_id' => $this->userId,
            'name' => 'uroo-admin',
            'full_name' => 'uroo-dev/uroo-admin',
            'description' => 'UROO.DEV Workspace — Operating system untuk developer',
            'url' => 'https://github.com/uroo-dev/uroo-admin',
            'language' => 'PHP',
            'stars' => 12,
            'forks' => 3,
            'open_issues' => 2,
            'default_branch' => 'main',
            'last_pushed_at' => now(),
        ]);

        Commit::create([
            'repository_id' => $repo->id,
            'sha' => 'a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0',
            'message' => 'feat: add credential vault with AES encryption',
            'author_name' => 'Dimas',
            'author_email' => 'dimas@uroo.dev',
            'branch' => 'main',
            'modified_files' => ['app/Providers/AppServiceProvider.php', 'routes/web.php'],
            'added_files' => ['Modules/Credential/Models/Credential.php', 'Modules/Credential/Services/CredentialService.php'],
            'additions' => 245,
            'deletions' => 12,
            'committed_at' => now()->subHours(2),
        ]);

        Commit::create([
            'repository_id' => $repo->id,
            'sha' => 'b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1',
            'message' => 'fix: invoice total calculation with tax and discount',
            'author_name' => 'Dimas',
            'author_email' => 'dimas@uroo.dev',
            'branch' => 'develop',
            'modified_files' => ['Modules/Invoice/Services/InvoiceService.php'],
            'additions' => 45,
            'deletions' => 8,
            'committed_at' => now()->subHours(5),
        ]);

        Commit::create([
            'repository_id' => $repo->id,
            'sha' => 'c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2',
            'message' => 'feat: dashboard stat cards and recent activities',
            'author_name' => 'Dimas',
            'author_email' => 'dimas@uroo.dev',
            'branch' => 'main',
            'modified_files' => ['resources/views/dashboard/index.blade.php'],
            'added_files' => ['app/Http/Controllers/DashboardController.php'],
            'additions' => 180,
            'deletions' => 0,
            'committed_at' => now()->subDay(),
        ]);
    }

    protected function seedCredentials(): void
    {
        $cred = new Credential();
        $cred->user_id = $this->userId;
        $cred->type = 'hosting';
        $cred->label = 'Production Server';
        $cred->username = 'root';
        $cred->password = 's3cur3P@ss!';
        $cred->is_favorite = true;
        $cred->save();

        $cred2 = new Credential();
        $cred2->user_id = $this->userId;
        $cred2->type = 'database';
        $cred2->label = 'MySQL Production';
        $cred2->username = 'admin_uroo';
        $cred2->password = 'db_P@ssw0rd!';
        $cred2->save();

        $cred3 = new Credential();
        $cred3->user_id = $this->userId;
        $cred3->type = 'api_key';
        $cred3->label = 'OpenAI API';
        $cred3->password = 'sk-proj-xxxxxxxxxxxx';
        $cred3->is_favorite = true;
        $cred3->save();

        $cred4 = new Credential();
        $cred4->user_id = $this->userId;
        $cred4->type = 'vps';
        $cred4->label = 'Staging Server';
        $cred4->username = 'deploy';
        $cred4->password = 'st@g!ngP@ss';
        $cred4->save();
    }

    protected function seedClients(): void
    {
        Client::create([
            'user_id' => $this->userId,
            'name' => 'PT Maju Jaya',
            'email' => 'info@majujaya.com',
            'phone' => '08123456789',
            'whatsapp' => '628123456789',
            'company' => 'PT Maju Jaya',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'status' => 'active',
        ]);

        Client::create([
            'user_id' => $this->userId,
            'name' => 'CV Kreatif Studio',
            'email' => 'hello@kreatifstudio.com',
            'phone' => '08765432100',
            'whatsapp' => '628765432100',
            'company' => 'CV Kreatif Studio',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'status' => 'active',
        ]);

        Client::create([
            'user_id' => $this->userId,
            'name' => 'Personal Project',
            'email' => '',
            'company' => '',
            'status' => 'active',
        ]);

        Client::create([
            'user_id' => $this->userId,
            'name' => 'PT Digital Solusi',
            'phone' => '08987654321',
            'company' => 'PT Digital Solusi',
            'status' => 'inactive',
        ]);
    }

    protected function seedInvoices(): void
    {
        Invoice::create([
            'user_id' => $this->userId,
            'client_id' => 1,
            'invoice_number' => 'INV-2026-07-0001',
            'items' => [
                ['name' => 'Website Development', 'qty' => 1, 'price' => 15000000],
                ['name' => 'UI/UX Design', 'qty' => 1, 'price' => 5000000],
            ],
            'subtotal' => 20000000,
            'tax_percent' => 11,
            'tax_amount' => 2200000,
            'total' => 22200000,
            'status' => 'paid',
            'due_date' => now()->subDays(5),
            'paid_at' => now()->subDays(2),
        ]);

        Invoice::create([
            'user_id' => $this->userId,
            'client_id' => 2,
            'invoice_number' => 'INV-2026-07-0002',
            'items' => [
                ['name' => 'Mobile App Development', 'qty' => 1, 'price' => 25000000],
            ],
            'subtotal' => 25000000,
            'tax_percent' => 11,
            'tax_amount' => 2750000,
            'total' => 27750000,
            'status' => 'pending',
            'due_date' => now()->addDays(10),
        ]);

        Invoice::create([
            'user_id' => $this->userId,
            'client_id' => 1,
            'invoice_number' => 'INV-2026-07-0003',
            'items' => [
                ['name' => 'Maintenance Bulanan', 'qty' => 1, 'price' => 2000000],
            ],
            'subtotal' => 2000000,
            'tax_percent' => 11,
            'tax_amount' => 220000,
            'total' => 2220000,
            'status' => 'overdue',
            'due_date' => now()->subDays(3),
        ]);
    }

    protected function seedProjects(): void
    {
        Project::create([
            'user_id' => $this->userId,
            'client_id' => 1,
            'name' => 'Company Profile Website',
            'description' => 'Website company profile dengan CMS',
            'category' => 'web',
            'status' => 'completed',
            'tech_stack' => ['Laravel', 'Tailwind CSS', 'MySQL'],
            'start_date' => now()->subMonths(3),
            'deadline' => now()->subMonth(),
            'completed_at' => now()->subMonth(),
        ]);

        Project::create([
            'user_id' => $this->userId,
            'client_id' => 2,
            'name' => 'E-Commerce Mobile App',
            'description' => 'Aplikasi e-commerce dengan Flutter',
            'category' => 'mobile',
            'status' => 'development',
            'tech_stack' => ['Flutter', 'Laravel API', 'PostgreSQL'],
            'start_date' => now()->subMonth(),
            'deadline' => now()->addMonths(2),
        ]);

        Project::create([
            'user_id' => $this->userId,
            'name' => 'UROO.DEV Workspace',
            'description' => 'OS untuk developer dan freelancer',
            'category' => 'web',
            'status' => 'development',
            'tech_stack' => ['Laravel', 'Livewire', 'Tailwind CSS', 'MySQL'],
            'start_date' => now()->subWeeks(2),
        ]);
    }

    protected function seedNotes(): void
    {
        Note::create([
            'user_id' => $this->userId,
            'title' => 'Cara Deploy Laravel di Ubuntu',
            'content' => "1. Update server\n2. Install PHP 8.3, Composer, Nginx\n3. Clone repo\n4. Setup .env\n5. Run migrations\n6. Setup queue worker\n7. Setup scheduler\n8. Setup SSL dengan Certbot",
            'category' => 'Server Setup',
            'tags' => ['laravel', 'deploy', 'ubuntu'],
            'is_pinned' => true,
        ]);

        Note::create([
            'user_id' => $this->userId,
            'title' => 'AI Prompt Templates',
            'content' => "## Code Review Prompt\n\nReview kode berikut...\n\n## Bug Fix Prompt\n\nBantu saya fix bug ini...",
            'category' => 'AI Prompt',
            'tags' => ['ai', 'prompt'],
            'is_favorite' => true,
        ]);

        Note::create([
            'user_id' => $this->userId,
            'title' => 'Docker Commands Cheatsheet',
            'content' => "docker ps -a\n docker compose up -d\n docker exec -it {container} bash\n docker logs -f {container}",
            'category' => 'Cheat Sheet',
            'tags' => ['docker', 'devops'],
        ]);
    }

    protected function seedBookmarks(): void
    {
        Bookmark::create([
            'user_id' => $this->userId,
            'title' => 'Laravel Documentation',
            'url' => 'https://laravel.com/docs',
            'description' => 'Official Laravel documentation',
            'category' => 'Laravel',
            'is_favorite' => true,
        ]);

        Bookmark::create([
            'user_id' => $this->userId,
            'title' => 'Tailwind CSS',
            'url' => 'https://tailwindcss.com',
            'description' => 'Utility-first CSS framework',
            'category' => 'CSS',
        ]);

        Bookmark::create([
            'user_id' => $this->userId,
            'title' => 'Livewire Docs',
            'url' => 'https://livewire.laravel.com',
            'description' => 'Full-stack framework for Laravel',
            'category' => 'Laravel',
            'is_favorite' => true,
        ]);

        Bookmark::create([
            'user_id' => $this->userId,
            'title' => 'GitHub',
            'url' => 'https://github.com',
            'description' => 'Version control platform',
            'category' => 'DevOps',
        ]);
    }

    protected function seedQualityControl(): void
    {
        $checklist = QualityChecklist::create([
            'user_id' => $this->userId,
            'title' => 'Pre-Deployment Checklist',
            'description' => 'Ceklist sebelum deploy ke production',
        ]);

        $items = [
            'APP_DEBUG = false',
            'APP_ENV = production',
            'Database Backup sudah dilakukan',
            'Storage link sudah aktif',
            'Queue runner sudah berjalan',
            'Scheduler sudah berjalan',
            'SSL Certificate aktif',
            'SMTP sudah terkonfigurasi',
            'SEO meta tags sudah diatur',
            'Responsive di semua device sudah dicek',
            'Performance testing sudah dilakukan',
        ];

        foreach ($items as $i => $item) {
            ChecklistItem::create([
                'checklist_id' => $checklist->id,
                'label' => $item,
                'is_checked' => $i < 6,
                'sort_order' => $i,
            ]);
        }

        $checklist2 = QualityChecklist::create([
            'user_id' => $this->userId,
            'title' => 'New Feature Release',
            'description' => 'Checklist untuk rilis fitur baru',
        ]);

        $items2 = [
            'Unit test sudah jalan',
            'Code review sudah dilakukan',
            'Dokumentasi sudah diupdate',
            'Changelog sudah diupdate',
            'Testing di staging sudah OK',
        ];

        foreach ($items2 as $i => $item) {
            ChecklistItem::create([
                'checklist_id' => $checklist2->id,
                'label' => $item,
                'is_checked' => $i < 3,
                'sort_order' => $i,
            ]);
        }
    }

    protected function seedIdeas(): void
    {
        AppIdea::create([
            'user_id' => $this->userId,
            'name' => 'Task Manager AI',
            'tagline' => 'Task manager dengan AI recommendations',
            'description' => 'Aplikasi task manager yang menggunakan AI untuk memprioritaskan tugas dan memberi saran produktivitas',
            'features' => ['AI Priority', 'Smart Scheduling', 'Team Collaboration', 'Time Tracking'],
            'tech_stack' => ['Laravel', 'React', 'OpenAI API', 'PostgreSQL'],
            'platform' => 'web',
            'status' => 'research',
            'priority' => 'high',
            'tags' => ['ai', 'productivity'],
        ]);

        AppIdea::create([
            'user_id' => $this->userId,
            'name' => 'Dev Portfolio Builder',
            'tagline' => 'Buat portfolio developer dengan drag & drop',
            'description' => 'Platform untuk developer membuat portfolio tanpa coding',
            'features' => ['Drag & Drop Builder', 'Template Gallery', 'Custom Domain', 'Analytics'],
            'tech_stack' => ['Laravel', 'Vue.js', 'Tailwind CSS'],
            'platform' => 'web',
            'status' => 'draft',
            'priority' => 'medium',
            'tags' => ['portfolio', 'no-code'],
        ]);

        AppIdea::create([
            'user_id' => $this->userId,
            'name' => 'Invoice Mobile App',
            'tagline' => 'Buat invoice dari HP',
            'description' => 'Aplikasi mobile untuk freelance yang sering bikin invoice di jalan',
            'features' => ['Quick Invoice', 'PDF Export', 'WhatsApp Share', 'Payment Tracking'],
            'tech_stack' => ['Flutter', 'Laravel API'],
            'platform' => 'mobile',
            'status' => 'development',
            'priority' => 'high',
            'tags' => ['freelance', 'invoice'],
        ]);
    }

    protected function seedBrainDump(): void
    {
        BrainDump::create([
            'user_id' => $this->userId,
            'content' => 'Ide untuk fitur baru: dark mode dengan toggle di navbar',
            'is_pinned' => true,
        ]);

        BrainDump::create([
            'user_id' => $this->userId,
            'content' => 'Cek harga VPS baru di DigitalOcean, ada promo $20 credit',
        ]);

        BrainDump::create([
            'user_id' => $this->userId,
            'content' => 'Beli domain baru untuk project portfolio',
            'is_pinned' => true,
        ]);

        BrainDump::create([
            'user_id' => $this->userId,
            'content' => 'Update Laravel ke versi terbaru minggu depan',
        ]);

        BrainDump::create([
            'user_id' => $this->userId,
            'content' => 'Buat video tutorial tentang Livewire v4',
            'is_archived' => true,
        ]);
    }

    protected function seedSavings(): void
    {
        $goal = SavingsGoal::create([
            'user_id' => $this->userId,
            'name' => 'MacBook Pro M4',
            'target_amount' => 35000000,
            'current_amount' => 12500000,
            'icon' => 'bx bxs-laptop',
            'color' => '#4F46E5',
            'deadline' => now()->addMonths(6),
        ]);

        SavingsTransaction::create([
            'goal_id' => $goal->id,
            'type' => 'deposit',
            'amount' => 5000000,
            'description' => 'Gajian bulan Juni',
        ]);

        SavingsTransaction::create([
            'goal_id' => $goal->id,
            'type' => 'deposit',
            'amount' => 2500000,
            'description' => 'Project freelance',
        ]);

        SavingsTransaction::create([
            'goal_id' => $goal->id,
            'type' => 'deposit',
            'amount' => 5000000,
            'description' => 'Gajian bulan Juli',
        ]);

        $goal2 = SavingsGoal::create([
            'user_id' => $this->userId,
            'name' => 'Dana Darurat',
            'target_amount' => 15000000,
            'current_amount' => 8000000,
            'icon' => 'bx bxs-shield',
            'color' => '#22C55E',
        ]);

        SavingsTransaction::create([
            'goal_id' => $goal2->id,
            'type' => 'deposit',
            'amount' => 5000000,
            'description' => 'Alokasi dana darurat',
        ]);

        SavingsTransaction::create([
            'goal_id' => $goal2->id,
            'type' => 'deposit',
            'amount' => 3000000,
            'description' => 'Tambahan dana darurat',
        ]);

        SavingsGoal::create([
            'user_id' => $this->userId,
            'name' => 'Liburan Bali',
            'target_amount' => 10000000,
            'current_amount' => 0,
            'icon' => 'bx bxs-plane',
            'color' => '#F59E0B',
            'deadline' => now()->addMonths(12),
        ]);
    }

    protected function seedSubscriptions(): void
    {
        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'DigitalOcean Droplet',
            'provider' => 'DigitalOcean',
            'category' => 'VPS',
            'monthly_cost' => 240000,
            'annual_cost' => 2640000,
            'billing_cycle' => 'monthly',
            'due_date' => now()->addDays(5),
            'payment_status' => 'unpaid',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'Domain uroo.dev',
            'provider' => 'Niagahoster',
            'category' => 'Domain',
            'monthly_cost' => 0,
            'annual_cost' => 250000,
            'billing_cycle' => 'yearly',
            'due_date' => now()->addMonths(3),
            'payment_status' => 'paid',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'GitHub Copilot',
            'provider' => 'GitHub',
            'category' => 'Tools',
            'monthly_cost' => 100000,
            'annual_cost' => 1200000,
            'billing_cycle' => 'monthly',
            'due_date' => now()->addDays(15),
            'payment_status' => 'paid',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'OpenAI API',
            'provider' => 'OpenAI',
            'category' => 'AI',
            'monthly_cost' => 200000,
            'annual_cost' => null,
            'billing_cycle' => 'monthly',
            'due_date' => now()->addDays(20),
            'payment_status' => 'paid',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'Spotify Premium',
            'provider' => 'Spotify',
            'category' => 'Entertainment',
            'monthly_cost' => 54900,
            'annual_cost' => 658800,
            'billing_cycle' => 'monthly',
            'due_date' => now()->addDays(7),
            'payment_status' => 'paid',
            'is_active' => true,
        ]);

        Subscription::create([
            'user_id' => $this->userId,
            'name' => 'Internet First Media',
            'provider' => 'First Media',
            'category' => 'Internet',
            'monthly_cost' => 350000,
            'annual_cost' => 4200000,
            'billing_cycle' => 'monthly',
            'due_date' => now()->addDays(12),
            'payment_status' => 'unpaid',
            'is_active' => true,
        ]);
    }
}