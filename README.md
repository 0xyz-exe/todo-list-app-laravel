# Todo List App - Laravel

A streamlined personal task and productivity tracker built with Laravel 13, featuring priority tagging, due date tracking, overdue alerts, and completion workflows.

Repository: https://github.com/0xyz-exe/todo-list-app-laravel

## Screenshots

| Tampilan | Preview |
|---|---|
| **Dasbor Tugas & Metrik Produktivitas**<br>Ringkasan statistik tugas (Total, Pending, Selesai, Terlambat), panel filter pencarian, dan daftar kartu tugas | ![Dasbor Tugas & Metrik Produktivitas](docs/screenshots/01-tasks-dashboard-overview.png) |
| **Formulir Pembuatan Tugas Baru**<br>Formulir intake pembuatan tugas baru dengan judul, deskripsi detail, prioritas (Low/Medium/High), dan batas waktu pengerjaan | ![Formulir Pembuatan Tugas Baru](docs/screenshots/02-create-task-intake.png) |
| **Inspeksi Detail Tugas & Timeline Status**<br>Halaman detail tugas dengan metadata tanggal jatuh tempo, waktu pembuatan, tombol checklist selesai, serta aksi edit dan hapus | ![Inspeksi Detail Tugas & Timeline Status](docs/screenshots/03-task-detail-inspection.png) |
| **Filter Tugas Berdasarkan Prioritas Tinggi**<br>Tampilan hasil filter tugas berkategori High Priority lengkap dengan peringatan tugas yang telah melewati batas waktu (Overdue) | ![Filter Tugas Berdasarkan Prioritas Tinggi](docs/screenshots/04-filtered-priority-tasks.png) |

## Features

- **Task Lifecycle:** Full task management with pending, in-progress, and completed states.
- **Priority Management:** Categorize tasks by priority levels (low, medium, high) with visual status indicators.
- **Due Date Tracking:** Smart scheduling with automatic overdue detection for past-due tasks.
- **One-Click Toggle:** Quick completion toggle with automatic timestamp auditing.
- **User Isolation:** Multi-user support with strict policy boundaries keeping tasks private to each user.
- **Laravel Breeze Auth:** Complete registration, authentication, and user profile management.

## Tech Stack

- **Framework:** Laravel 13.38.0
- **Auth:** Laravel Breeze 2.4
- **Database:** SQLite
- **Frontend:** Blade, Tailwind CSS

## Installation

```bash
git clone https://github.com/0xyz-exe/todo-list-app-laravel.git
cd todo-list-app-laravel
composer install
npm install
npm run build
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

## Database Schema

**tasks table:**
- `id` (bigint, primary key)
- `user_id` (foreign key)
- `title` (string)
- `description` (text, nullable)
- `priority` (enum: low, medium, high)
- `due_date` (date, nullable)
- `status` (string, default: pending)
- `completed_at` (timestamp, nullable)
- `created_at`, `updated_at` (timestamps)

## License

MIT License.
