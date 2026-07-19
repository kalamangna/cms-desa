# AGENTS.md

# Laravel CMS Website Desa

## Project Overview

Ini adalah proyek CMS Website Desa berbasis Laravel dan Tailwind CSS.

Tujuan utama proyek:

- Mudah dipelihara
- Aman
- Cepat
- Responsif
- Mudah digunakan oleh operator desa
- Mengikuti best practice Laravel terbaru

---

# Tech Stack

- Laravel 12
- PHP 8.3+
- Tailwind CSS
- Vite
- MySQL / MariaDB

---

# General Rules

Selalu:

- pahami konteks sebelum mengubah kode
- analisis terlebih dahulu
- jelaskan rencana perubahan
- lakukan perubahan seminimal mungkin
- pertahankan struktur proyek yang ada

Jangan membuat perubahan besar tanpa persetujuan.

---

# Coding Standards

Ikuti:

- PSR-12
- Laravel Best Practices
- SOLID Principles
- Clean Code

Gunakan:

- Type Hint
- Return Type
- Constructor Property Promotion jika memungkinkan

Hindari:

- function terlalu panjang
- nested if berlebihan
- duplicate code

---

# Architecture

Controller harus tetap tipis.

Business logic berada di:

- Service
- Action
- Helper (jika memang diperlukan)

Jangan menaruh business logic besar di Controller.

---

# Validation

Selalu gunakan:

- Form Request

Jangan melakukan validasi langsung di Controller kecuali sangat sederhana.

---

# Database

Gunakan:

- Migration
- Seeder
- Factory

Gunakan:

- Eloquent Relationship

Hindari:

- Query Builder di View
- Raw Query jika tidak diperlukan

Optimalkan:

- eager loading
- indexing
- pagination

---

# Blade

Gunakan:

- Blade Components
- Blade Layout

Hindari:

- query database di Blade
- logika kompleks di Blade

---

# Tailwind CSS

Gunakan utility class Tailwind.

Prioritaskan:

- reusable component
- responsive design
- dark mode compatibility (jika diperlukan)

Jangan menggunakan Bootstrap.

---

# JavaScript

Gunakan JavaScript seperlunya.

Jika memungkinkan:

- gunakan Alpine.js

Hindari library besar jika fitur dapat dibuat sederhana.

---

# Security

Selalu periksa:

- Authorization
- Authentication
- CSRF
- XSS
- Mass Assignment
- File Upload
- Validation

Jangan pernah:

- menghapus middleware keamanan
- menonaktifkan CSRF
- menyimpan password tanpa hashing

---

# Performance

Prioritaskan:

- eager loading
- cache jika diperlukan
- pagination
- lazy loading asset

Hindari:

- N+1 Query
- query berulang

---

# UI/UX

Target pengguna:

Operator Website Desa.

UI harus:

- sederhana
- konsisten
- mudah dipahami
- mobile friendly
- aksesibel

---

# Before Editing

Sebelum mengubah kode:

1. Jelaskan masalah.
2. Jelaskan solusi.
3. Sebutkan file yang akan diubah.
4. Jelaskan dampaknya.

---

# After Editing

Setelah selesai:

Berikan ringkasan:

- file yang diubah
- alasan perubahan
- dampak perubahan
- potensi risiko
- cara pengujian

---

# Forbidden

Jangan pernah:

- mengubah file .env
- mengubah vendor/
- menghapus migration lama
- mengubah composer.json tanpa alasan jelas
- mengubah package-lock.json atau composer.lock jika tidak diperlukan

---

# Git

Usahakan setiap perubahan:

- kecil
- fokus
- mudah direview

---

# Language

Gunakan:

- Bahasa Indonesia untuk penjelasan kepada developer.
- Bahasa Inggris untuk kode, nama class, method, komentar teknis, dan commit message.

---

# Preferred Workflow

1. Analisis
2. Review
3. Rencana
4. Implementasi
5. Testing
6. Dokumentasi

Jangan langsung melakukan implementasi tanpa analisis.

---

# Preferred Response Style

Jawaban harus:

- ringkas
- jelas
- teknis
- berdasarkan best practice Laravel terbaru

Jika ada beberapa solusi:

- jelaskan kelebihan dan kekurangannya
- rekomendasikan solusi terbaik beserta alasannya.
