<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Foreign key untuk karyawan - department_id (jika belum ada)
        if (Schema::hasTable('karyawan') && !$this->constraintExists('karyawan', 'karyawan_department_id_foreign')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->foreign('department_id')
                      ->references('id')
                      ->on('departments')
                      ->onDelete('cascade');
            });
        }

        // Foreign key untuk barang - kategori_id (jika belum ada)
        if (Schema::hasTable('barang') && !$this->constraintExists('barang', 'barang_kategori_id_foreign')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->foreign('kategori_id')
                      ->references('id')
                      ->on('kategori')
                      ->onDelete('cascade');
            });
        }

        // Foreign keys untuk transaksi (jika belum ada)
        if (Schema::hasTable('transaksi')) {
            if (!$this->constraintExists('transaksi', 'transaksi_barang_id_foreign')) {
                Schema::table('transaksi', function (Blueprint $table) {
                    $table->foreign('barang_id')
                          ->references('id')
                          ->on('barang')
                          ->onDelete('cascade');
                });
            }
            
            if (!$this->constraintExists('transaksi', 'transaksi_user_id_foreign')) {
                Schema::table('transaksi', function (Blueprint $table) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
        }

        // Foreign keys untuk permintaan_barang (jika belum ada)
        if (Schema::hasTable('permintaan_barang')) {
            if (!$this->constraintExists('permintaan_barang', 'permintaan_barang_user_id_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
            
            if (!$this->constraintExists('permintaan_barang', 'permintaan_barang_barang_id_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->foreign('barang_id')
                          ->references('id')
                          ->on('barang')
                          ->onDelete('cascade');
                });
            }
            
            if (!$this->constraintExists('permintaan_barang', 'permintaan_barang_disetujui_oleh_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->foreign('disetujui_oleh')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
        }

        // Foreign keys untuk penggunaan_kendaraan (jika belum ada)
        if (Schema::hasTable('penggunaan_kendaraan')) {
            if (!$this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_kendaraan_id_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->foreign('kendaraan_id')
                          ->references('id')
                          ->on('kendaraan')
                          ->onDelete('cascade');
                });
            }
            
            if (!$this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_user_id_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
            
            if (!$this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_disetujui_oleh_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->foreign('disetujui_oleh')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
        }
    }

    public function down()
    {
        // Drop foreign keys hanya jika ada
        if (Schema::hasTable('karyawan') && $this->constraintExists('karyawan', 'karyawan_department_id_foreign')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropForeign(['department_id']);
            });
        }

        if (Schema::hasTable('barang') && $this->constraintExists('barang', 'barang_kategori_id_foreign')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->dropForeign(['kategori_id']);
            });
        }

        if (Schema::hasTable('transaksi')) {
            if ($this->constraintExists('transaksi', 'transaksi_barang_id_foreign')) {
                Schema::table('transaksi', function (Blueprint $table) {
                    $table->dropForeign(['barang_id']);
                });
            }
            
            if ($this->constraintExists('transaksi', 'transaksi_user_id_foreign')) {
                Schema::table('transaksi', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }
        }

        if (Schema::hasTable('permintaan_barang')) {
            if ($this->constraintExists('permintaan_barang', 'permintaan_barang_user_id_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }
            
            if ($this->constraintExists('permintaan_barang', 'permintaan_barang_barang_id_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->dropForeign(['barang_id']);
                });
            }
            
            if ($this->constraintExists('permintaan_barang', 'permintaan_barang_disetujui_oleh_foreign')) {
                Schema::table('permintaan_barang', function (Blueprint $table) {
                    $table->dropForeign(['disetujui_oleh']);
                });
            }
        }

        if (Schema::hasTable('penggunaan_kendaraan')) {
            if ($this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_kendaraan_id_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->dropForeign(['kendaraan_id']);
                });
            }
            
            if ($this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_user_id_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            }
            
            if ($this->constraintExists('penggunaan_kendaraan', 'penggunaan_kendaraan_disetujui_oleh_foreign')) {
                Schema::table('penggunaan_kendaraan', function (Blueprint $table) {
                    $table->dropForeign(['disetujui_oleh']);
                });
            }
        }
    }

    /**
     * Check if a foreign key constraint exists
     */
    private function constraintExists($table, $constraintName)
    {
        $conn = Schema::getConnection();
        $databaseName = $conn->getDatabaseName();
        
        $result = $conn->selectOne("
            SELECT COUNT(*) as count 
            FROM information_schema.table_constraints 
            WHERE constraint_schema = ? 
            AND table_name = ? 
            AND constraint_name = ? 
            AND constraint_type = 'FOREIGN KEY'
        ", [$databaseName, $table, $constraintName]);
        
        return $result->count > 0;
    }
};