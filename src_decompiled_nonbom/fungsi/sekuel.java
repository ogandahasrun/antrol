/*
 * Decompiled with CFR 0.152.
 * 
 * Could not load the following classes:
 *  uz.ncipro.calendar.JDateTimePicker
 */
package fungsi;

import fungsi.koneksiDB;
import java.awt.Canvas;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.geom.AffineTransform;
import java.awt.image.BufferedImage;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.nio.ByteBuffer;
import java.nio.channels.FileChannel;
import java.sql.Blob;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import javax.swing.ImageIcon;
import javax.swing.JComboBox;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JTable;
import javax.swing.JTextArea;
import javax.swing.JTextField;
import javax.swing.table.TableColumn;
import uz.ncipro.calendar.JDateTimePicker;

public final class sekuel {
    private ImageIcon icon = null;
    private ImageIcon iconThumbnail = null;
    private String folder;
    private final Connection connect = koneksiDB.condb();
    private PreparedStatement ps;
    private ResultSet rs;
    private int angka = 0;
    private double angka2 = 0.0;
    private String dicari = "";
    private Date tanggal = new Date();
    private boolean bool = false;
    private DecimalFormat df2 = new DecimalFormat("####");

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value, String sama) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan2(String table, String value, String sama) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public boolean menyimpantf(String table, String value, String sama) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
            return true;
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
            JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            return false;
        }
    }

    public boolean menyimpantf2(String table, String value, String sama) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
            return true;
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
            return false;
        }
    }

    public boolean menyimpantf(String table, String value, int i, String[] a, String acuan_field, String update, int j, String[] b) {
        this.bool = false;
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.angka = 1;
            while (this.angka <= i) {
                this.ps.setString(this.angka, a[this.angka - 1]);
                ++this.angka;
            }
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
            this.bool = true;
        }
        catch (Exception e) {
            try {
                this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
                this.angka = 1;
                while (this.angka <= j) {
                    this.ps.setString(this.angka, b[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                if (this.ps != null) {
                    this.ps.close();
                }
                this.bool = true;
            }
            catch (Exception e2) {
                this.bool = false;
                System.out.println("Notifikasi : " + e2);
            }
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value, String sama, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan2(String table, String value, String sama, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public boolean menyimpantf(String table, String value, String sama, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.angka = 1;
            while (this.angka <= i) {
                this.ps.setString(this.angka, a[this.angka - 1]);
                ++this.angka;
            }
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
            return true;
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
            if (e.toString().contains("Duplicate")) {
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            } else {
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Ada kesalahan Query...!");
            }
            return false;
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public boolean menyimpantf2(String table, String value, String sama, int i, String[] a) {
        this.bool = true;
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                this.bool = true;
            }
            catch (Exception e) {
                this.bool = false;
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            this.bool = false;
            System.out.println("Notifikasi : " + e);
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan2(String table, String value, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi " + table + " : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception exception) {
            // empty catch block
        }
    }

    public void menyimpan(String table, String value, int i, String[] a, String acuan_field, String update, int j, String[] b) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.angka = 1;
            while (this.angka <= i) {
                this.ps.setString(this.angka, a[this.angka - 1]);
                ++this.angka;
            }
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
        }
        catch (Exception e) {
            try {
                this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
                this.angka = 1;
                while (this.angka <= j) {
                    this.ps.setString(this.angka, b[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                if (this.ps != null) {
                    this.ps.close();
                }
            }
            catch (Exception e2) {
                System.out.println("Notifikasi : " + e2);
            }
        }
    }

    public void menyimpan3(String table, String value, int i, String[] a, String acuan_field, String update, int j, String[] b) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            this.angka = 1;
            while (this.angka <= i) {
                this.ps.setString(this.angka, a[this.angka - 1]);
                ++this.angka;
            }
            this.ps.executeUpdate();
            JOptionPane.showMessageDialog(null, "Proses simpan berhasil..!!");
            if (this.ps != null) {
                this.ps.close();
            }
        }
        catch (Exception e) {
            try {
                this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
                this.angka = 1;
                while (this.angka <= j) {
                    this.ps.setString(this.angka, b[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                JOptionPane.showMessageDialog(null, "Proses simpan berhasil..!!");
                if (this.ps != null) {
                    this.ps.close();
                }
            }
            catch (Exception e2) {
                System.out.println("Notifikasi : " + e2);
            }
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ")");
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public void menyimpan(String table, String isisimpan, String isiedit, String acuan_field) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + isisimpan + ")");
            this.ps.executeUpdate();
            if (this.ps != null) {
                this.ps.close();
            }
        }
        catch (Exception e) {
            try {
                this.ps = this.connect.prepareStatement("update " + table + " set " + isiedit + " where " + acuan_field);
                this.ps.executeUpdate();
                if (this.ps != null) {
                    this.ps.close();
                }
            }
            catch (Exception ex) {
                System.out.println("Notifikasi Edit : " + ex);
            }
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value, String sama, JTextField AlmGb) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ",?)");
            try {
                this.ps.setBinaryStream(1, (InputStream)new FileInputStream(AlmGb.getText()), new File(AlmGb.getText()).length());
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void menyimpan(String table, String value, String sama, JTextField AlmGb, JTextField AlmPhoto) {
        try {
            this.ps = this.connect.prepareStatement("insert into " + table + " values(" + value + ",?,?)");
            try {
                this.ps.setBinaryStream(1, (InputStream)new FileInputStream(AlmGb.getText()), new File(AlmGb.getText()).length());
                this.ps.setBinaryStream(2, (InputStream)new FileInputStream(AlmPhoto.getText()), new File(AlmPhoto.getText()).length());
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, gagal menyimpan data. Kemungkinan ada " + sama + " yang sama dimasukkan sebelumnya...!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void meghapus(String table, String field, String nilai_field) {
        try {
            this.ps = this.connect.prepareStatement("delete from " + table + " where " + field + "=?");
            try {
                this.ps.setString(1, nilai_field);
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, data gagal dihapus. Kemungkinan data tersebut masih dipakai di table lain...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void meghapus2(String table, String field, String nilai_field) {
        try {
            this.ps = this.connect.prepareStatement("delete from " + table + " where " + field + "=?");
            try {
                this.ps.setString(1, nilai_field);
                this.ps.executeUpdate();
                JOptionPane.showMessageDialog(null, "Proses hapus berhasil...!!!!");
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, data gagal dihapus. Kemungkinan data tersebut masih dipakai di table lain...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void meghapus3(String table, String field, String nilai_field) {
        try {
            this.ps = this.connect.prepareStatement("delete from " + table + " where " + field + "=?");
            try {
                this.ps.setString(1, nilai_field);
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void mengedit(String table, String acuan_field, String update) {
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Gagal Mengedit. Mungkin kode sudah digunakan sebelumnya...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public boolean mengedittf(String table, String acuan_field, String update) {
        this.bool = true;
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.ps.executeUpdate();
                this.bool = true;
            }
            catch (Exception e) {
                this.bool = false;
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Gagal Mengedit. Mungkin kode sudah digunakan sebelumnya...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            this.bool = false;
            System.out.println("Notifikasi : " + e);
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void mengedit(String table, String acuan_field, String update, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Gagal Mengedit. Periksa kembali data...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void mengedit2(String table, String acuan_field, String update, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                JOptionPane.showMessageDialog(null, "Proses edit berhasil...!!!!");
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Gagal mengedit. Periksa kembali data...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void mengedit3(String table, String acuan_field, String update, int i, String[] a) {
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public boolean mengedittf(String table, String acuan_field, String update, int i, String[] a) {
        this.bool = true;
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                this.bool = true;
            }
            catch (Exception e) {
                this.bool = false;
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Gagal Mengedit. Periksa kembali data...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            this.bool = false;
            System.out.println("Notifikasi : " + e);
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void mengedit(String table, String acuan_field, String update, JTextField AlmGb) {
        try {
            this.ps = this.connect.prepareStatement("update " + table + " set " + update + " where " + acuan_field);
            try {
                this.ps.setBinaryStream(1, (InputStream)new FileInputStream(AlmGb.getText()), new File(AlmGb.getText()).length());
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Pilih dulu data yang mau anda edit...\n Klik data pada table untuk memilih...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public void query(String qry) {
        try {
            this.ps = this.connect.prepareStatement(qry);
            try {
                this.ps.executeQuery();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Query tidak bisa dijalankan...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public void queryu(String qry) {
        try {
            this.ps = this.connect.prepareStatement(qry);
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Query tidak bisa dijalankan...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public boolean queryutf(String qry) {
        this.bool = false;
        try {
            this.ps = this.connect.prepareStatement(qry);
            try {
                this.ps.executeUpdate();
                this.bool = true;
            }
            catch (Exception e) {
                this.bool = false;
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Query tidak bisa dijalankan...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            this.bool = false;
            System.out.println("Notifikasi : " + e);
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void queryu(String qry, String parameter) {
        try {
            this.ps = this.connect.prepareStatement(qry);
            try {
                this.ps.setString(1, parameter);
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
                JOptionPane.showMessageDialog(null, "Maaf, Query tidak bisa dijalankan...!!!!");
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public void queryu2(String qry) {
        try {
            this.ps = this.connect.prepareStatement(qry);
            try {
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void queryu2(String qry, int i, String[] a) {
        try {
            try {
                this.ps = this.connect.prepareStatement(qry);
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public boolean queryu2tf(String qry, int i, String[] a) {
        this.bool = false;
        try {
            try {
                this.ps = this.connect.prepareStatement(qry);
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
                this.bool = true;
            }
            catch (Exception e) {
                this.bool = false;
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.bool;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void queryu3(String qry, int i, String[] a) {
        try {
            try {
                this.ps = this.connect.prepareStatement(qry);
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void queryu4(String qry, int i, String[] a) {
        try {
            try {
                this.ps = this.connect.prepareStatement(qry);
                this.angka = 1;
                while (this.angka <= i) {
                    this.ps.setString(this.angka, a[this.angka - 1]);
                    ++this.angka;
                }
                this.ps.executeUpdate();
            }
            catch (Exception exception) {
            }
            finally {
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception exception) {
            // empty catch block
        }
    }

    public void AutoComitFalse() {
        try {
            this.connect.setAutoCommit(false);
        }
        catch (Exception exception) {
            // empty catch block
        }
    }

    public void AutoComitTrue() {
        try {
            this.connect.setAutoCommit(true);
        }
        catch (Exception exception) {
            // empty catch block
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JComboBox cmb) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    String dicari = this.rs.getString(1);
                    cmb.setSelectedItem(dicari);
                } else {
                    cmb.setSelectedItem("");
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JDateTimePicker dtp) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    try {
                        dtp.setDisplayFormat("yyyy-MM-dd");
                        dtp.setDate(new SimpleDateFormat("yyyy-MM-dd").parse(this.rs.getString(1)));
                        dtp.setDisplayFormat("dd-MM-yyyy");
                    }
                    catch (Exception ex) {
                        System.out.println(ex);
                    }
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JTextField txt) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.rs.getString(1));
                } else {
                    txt.setText("");
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public int cariRegistrasi(String norawat) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement("select count(billing.no_rawat) from billing where billing.no_rawat=?");
            try {
                this.ps.setString(1, norawat);
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    this.angka = this.rs.getInt(1);
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println(e);
        }
        return this.angka;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JTextField txt, String kunci) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, kunci);
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.rs.getString(1));
                } else {
                    txt.setText("");
                }
            }
            catch (SQLException e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JTextArea txt, String kunci) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, kunci);
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.rs.getString(1));
                } else {
                    txt.setText("");
                }
            }
            catch (SQLException e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsi(String sql, JLabel txt) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.rs.getString(1));
                } else {
                    txt.setText("");
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public String cariIsi(String sql) {
        this.dicari = "";
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.dicari = this.rs.next() ? this.rs.getString(1) : "";
            }
            catch (Exception e) {
                this.dicari = "";
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.dicari;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public ByteArrayInputStream cariGambar(String sql) {
        ByteArrayInputStream inputStream = null;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    inputStream = new ByteArrayInputStream(this.rs.getBytes(1));
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return inputStream;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public String cariIsi(String sql, String data) {
        this.dicari = "";
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.rs = this.ps.executeQuery();
                this.dicari = this.rs.next() ? this.rs.getString(1) : "";
            }
            catch (Exception e) {
                this.dicari = "";
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.dicari;
    }

    public Date cariIsi2(String sql) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.tanggal = this.rs.next() ? this.rs.getDate(1) : new Date();
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.tanggal;
    }

    public Integer cariInteger(String sql) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.angka = this.rs.next() ? this.rs.getInt(1) : 0;
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    public Integer cariIntegerCount(String sql) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                while (this.rs.next()) {
                    this.angka += this.rs.getInt(1);
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public Integer cariInteger(String sql, String data) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.rs = this.ps.executeQuery();
                this.angka = this.rs.next() ? this.rs.getInt(1) : 0;
            }
            catch (Exception e) {
                this.angka = 0;
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public Integer cariInteger(String sql, String data, String data2) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.ps.setString(2, data2);
                this.rs = this.ps.executeQuery();
                this.angka = this.rs.next() ? this.rs.getInt(1) : 0;
            }
            catch (Exception e) {
                this.angka = 0;
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public Integer cariInteger(String sql, String data, String data2, String data3) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.ps.setString(2, data2);
                this.ps.setString(3, data3);
                this.rs = this.ps.executeQuery();
                this.angka = this.rs.next() ? this.rs.getInt(1) : 0;
            }
            catch (Exception e) {
                this.angka = 0;
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    public Integer cariInteger2(String sql) {
        this.angka = 0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.rs.last();
                this.angka = this.rs.getRow();
                if (this.angka < 1) {
                    this.angka = 0;
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsiAngka(String sql, JTextField txt) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.df2.format(this.rs.getDouble(1)));
                } else {
                    txt.setText("0");
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariIsiAngka(String sql, JLabel txt) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    txt.setText(this.df2.format(this.rs.getDouble(1)));
                } else {
                    txt.setText("0");
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public double cariIsiAngka(String sql) {
        this.angka2 = 0.0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.angka2 = this.rs.next() ? this.rs.getDouble(1) : 0.0;
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka2;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public double cariIsiAngka(String sql, String data) {
        this.angka2 = 0.0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.rs = this.ps.executeQuery();
                this.angka2 = this.rs.next() ? this.rs.getDouble(1) : 0.0;
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka2;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public double cariIsiAngka2(String sql, String data, String data2) {
        this.angka2 = 0.0;
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.ps.setString(1, data);
                this.ps.setString(2, data2);
                this.rs = this.ps.executeQuery();
                this.angka2 = this.rs.next() ? this.rs.getDouble(1) : 0.0;
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.angka2;
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariGambar(String sql, JLabel txt) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                if (this.rs.next()) {
                    this.icon = new ImageIcon(this.rs.getBlob(1).getBytes(1L, (int)this.rs.getBlob(1).length()));
                    this.createThumbnail();
                    txt.setIcon(this.icon);
                } else {
                    txt.setText(null);
                }
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    /*
     * WARNING - Removed try catching itself - possible behaviour change.
     */
    public void cariGambar(String sql, Canvas txt, String text) {
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                int I = 0;
                while (this.rs.next()) {
                    ((Painter)txt).setImage(this.gambar(text));
                    Blob blob = this.rs.getBlob(5);
                    ((Painter)txt).setImageIcon(new ImageIcon(blob.getBytes(1L, (int)blob.length())));
                    ++I;
                }
            }
            catch (Exception ex) {
                this.cetak(ex.toString());
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
    }

    public String cariString(String sql) {
        this.dicari = "";
        try {
            this.ps = this.connect.prepareStatement(sql);
            try {
                this.rs = this.ps.executeQuery();
                this.dicari = this.rs.next() ? this.rs.getString(1) : "";
            }
            catch (Exception e) {
                System.out.println("Notifikasi : " + e);
            }
            finally {
                if (this.rs != null) {
                    this.rs.close();
                }
                if (this.ps != null) {
                    this.ps.close();
                }
            }
        }
        catch (Exception e) {
            System.out.println("Notifikasi : " + e);
        }
        return this.dicari;
    }

    private String gambar(String id) {
        return this.folder + File.separator + id.trim() + ".jpg";
    }

    public void Tabel(JTable tb, int[] lebar) {
        tb.setAutoResizeMode(0);
        this.angka = tb.getColumnCount();
        for (int i = 0; i < this.angka; ++i) {
            TableColumn tbc = tb.getColumnModel().getColumn(i);
            tbc.setPreferredWidth(lebar[i]);
        }
    }

    private void createThumbnail() {
        int maxDim = 150;
        try {
            Image inImage = this.icon.getImage();
            double scale = (double)maxDim / (double)inImage.getHeight(null);
            if (inImage.getWidth(null) > inImage.getHeight(null)) {
                scale = (double)maxDim / (double)inImage.getWidth(null);
            }
            int scaledW = (int)(scale * (double)inImage.getWidth(null));
            int scaledH = (int)(scale * (double)inImage.getHeight(null));
            BufferedImage outImage = new BufferedImage(scaledW, scaledH, 1);
            AffineTransform tx = new AffineTransform();
            if (scale < 1.0) {
                tx.scale(scale, scale);
            }
            Graphics2D g2d = outImage.createGraphics();
            g2d.drawImage(inImage, tx, null);
            g2d.dispose();
            this.iconThumbnail = new ImageIcon(outImage);
        }
        catch (Exception exception) {
            // empty catch block
        }
    }

    private void cetak(String str) {
        System.out.println(str);
    }

    public class Painter
    extends Canvas {
        Image image;

        private void setImage(String file) {
            URL url = null;
            try {
                url = new File(file).toURI().toURL();
            }
            catch (MalformedURLException ex) {
                this.cetak(ex.toString());
            }
            this.image = this.getToolkit().getImage(url);
            this.repaint();
        }

        private void setImageIcon(ImageIcon file) {
            this.image = file.getImage();
            this.repaint();
        }

        @Override
        public void paint(Graphics g) {
            double d = this.image.getHeight(this) / this.getHeight();
            double w = (double)this.image.getWidth(this) / d;
            double x = (double)(this.getWidth() / 2) - w / 2.0;
            g.drawImage(this.image, (int)x, 0, (int)w, this.getHeight(), this);
        }

        private void cetak(String str) {
            System.out.println(str);
        }
    }

    public class NIOCopier {
        public NIOCopier(String asal, String tujuan) throws IOException {
            FileOutputStream outFile;
            try (FileInputStream inFile = new FileInputStream(asal);){
                FileChannel outChannel;
                outFile = new FileOutputStream(tujuan);
                try (FileChannel inChannel = inFile.getChannel();){
                    outChannel = outFile.getChannel();
                    ByteBuffer buffer = ByteBuffer.allocate(0x100000);
                    while (inChannel.read(buffer) != -1) {
                        buffer.flip();
                        while (buffer.hasRemaining()) {
                            outChannel.write(buffer);
                        }
                        buffer.clear();
                    }
                }
                outChannel.close();
            }
            outFile.close();
        }
    }
}

