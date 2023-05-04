# Mini E-Commerce Laravel 10

## Instalasi

1. Clone project dari github dengan git clone

```bash
git clone https://github.com/Kyotazel/ordo-test.git
```

2. Copy .env.example ke .env dan ubah serta tambahkan berikut

```bash
DB_CONNECTION=mysql
DB_HOST=database_host
DB_PORT=database_port
DB_DATABASE=database_name
DB_USERNAME=database_username
DB_PASSWORD=database_password

FILESYSTEM_DISK=public

MIDTRANS_IS_PRODUCTION=false
MIDTRANS_MERCHANT_ID=merchant-id
MIDTRANS_CLIENT_KEY=SB-Mid-client-key
MIDTRANS_SERVER_KEY=SB-Mid-server-key

```

3. Install dengan composer

```bash
composer install
```

4. Migrasi Database

```bash
php artisan migrate
```

5. Sambungkan link storage ke publik

```bash
php artisan storage:link
```

6. Jalankan project laravel dan buka di browser

```bash
php artisan serve
```



## Admin Website
1. Masuk ke url pada browser
2. Register dengan email dan password
3. Login menggunakan akun yang sudah register

### Kategori
1. Create Kategori
    - Masukkan Nama kategori dan gambar dan tekan submit
2. Update Kategori
    - Ubah yang diperlukan dan tekan submit
3. Delete Kategori
    - Tekan tombol delete untuk menghapus kategori

### Produk
1. Data Produk yang ditambahkan oleh Seller

### Seller
1. Data Seller yang terdaftar

### User
1. Data User yang terdaftar


## API

Jalankan Collection Postman dari link dibawah dan run in postman
[Collection Postman](https://documenter.getpostman.com/view/23467537/2s93eVXtXj#3168c730-82c4-4d89-ba0b-713ff8e9d397)

Link tersebut juga merupakan dokumentasi postman