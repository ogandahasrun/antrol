/*
 * Decompiled with CFR 0.152.
 * 
 * Could not load the following classes:
 *  org.apache.http.conn.scheme.Scheme
 *  org.apache.http.conn.scheme.SchemeSocketFactory
 *  org.apache.http.conn.ssl.SSLSocketFactory
 *  org.springframework.http.client.ClientHttpRequestFactory
 *  org.springframework.http.client.HttpComponentsClientHttpRequestFactory
 *  org.springframework.security.crypto.codec.Base64
 *  org.springframework.web.client.RestTemplate
 */
package fungsi;

import fungsi.ApiBPJSAesKeySpec;
import fungsi.ApiBPJSEnc;
import fungsi.ApiBPJSLZString;
import fungsi.koneksiDB;
import java.io.UnsupportedEncodingException;
import java.security.GeneralSecurityException;
import java.security.InvalidAlgorithmParameterException;
import java.security.InvalidKeyException;
import java.security.KeyManagementException;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.security.cert.CertificateException;
import java.security.cert.X509Certificate;
import javax.crypto.BadPaddingException;
import javax.crypto.IllegalBlockSizeException;
import javax.crypto.Mac;
import javax.crypto.NoSuchPaddingException;
import javax.crypto.spec.SecretKeySpec;
import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import org.apache.http.conn.scheme.Scheme;
import org.apache.http.conn.scheme.SchemeSocketFactory;
import org.apache.http.conn.ssl.SSLSocketFactory;
import org.springframework.http.client.ClientHttpRequestFactory;
import org.springframework.http.client.HttpComponentsClientHttpRequestFactory;
import org.springframework.security.crypto.codec.Base64;
import org.springframework.web.client.RestTemplate;

public class ApiMobileJKN {
    private String Key;
    private String Consid;
    private String salt;
    private String generateHmacSHA256Signature;
    private byte[] hmacData;
    private Mac mac;
    private long millis;
    private SSLContext sslContext;
    private SSLSocketFactory sslFactory;
    private SecretKeySpec secretKey;
    private Scheme scheme;
    private HttpComponentsClientHttpRequestFactory factory;
    private ApiBPJSAesKeySpec mykey;

    public ApiMobileJKN() {
        try {
            this.Key = koneksiDB.SECRETKEYAPIMOBILEJKN();
            this.Consid = koneksiDB.CONSIDAPIMOBILEJKN();
        }
        catch (Exception ex) {
            System.out.println("Notifikasi : " + ex);
        }
    }

    public String getHmac(String utc) {
        this.salt = this.Consid + "&" + utc;
        this.generateHmacSHA256Signature = null;
        try {
            this.generateHmacSHA256Signature = this.generateHmacSHA256Signature(this.salt, this.Key);
        }
        catch (GeneralSecurityException e) {
            System.out.println("Error Signature : " + e);
            e.printStackTrace();
        }
        return this.generateHmacSHA256Signature;
    }

    public String generateHmacSHA256Signature(String data, String key) throws GeneralSecurityException {
        this.hmacData = null;
        try {
            this.secretKey = new SecretKeySpec(key.getBytes("UTF-8"), "HmacSHA256");
            this.mac = Mac.getInstance("HmacSHA256");
            this.mac.init(this.secretKey);
            this.hmacData = this.mac.doFinal(data.getBytes("UTF-8"));
            return new String(Base64.encode((byte[])this.hmacData), "UTF-8");
        }
        catch (UnsupportedEncodingException e) {
            System.out.println("Error Generate HMac: e");
            throw new GeneralSecurityException(e);
        }
    }

    public long GetUTCdatetimeAsString() {
        this.millis = System.currentTimeMillis();
        return this.millis / 1000L;
    }

    public String Decrypt(String data, String utc) throws NoSuchPaddingException, NoSuchAlgorithmException, InvalidAlgorithmParameterException, InvalidKeyException, BadPaddingException, IllegalBlockSizeException {
        System.out.println(data);
        this.mykey = ApiBPJSEnc.generateKey(this.Consid + this.Key + utc);
        data = ApiBPJSEnc.decrypt(data, this.mykey.getKey(), this.mykey.getIv());
        data = ApiBPJSLZString.decompressFromEncodedURIComponent(data);
        return data;
    }

    public RestTemplate getRest() throws NoSuchAlgorithmException, KeyManagementException {
        this.sslContext = SSLContext.getInstance("SSL");
        TrustManager[] trustManagers = new TrustManager[]{new X509TrustManager(){

            @Override
            public X509Certificate[] getAcceptedIssuers() {
                return null;
            }

            @Override
            public void checkServerTrusted(X509Certificate[] arg0, String arg1) throws CertificateException {
            }

            @Override
            public void checkClientTrusted(X509Certificate[] arg0, String arg1) throws CertificateException {
            }
        }};
        this.sslContext.init(null, trustManagers, new SecureRandom());
        this.sslFactory = new SSLSocketFactory(this.sslContext, SSLSocketFactory.ALLOW_ALL_HOSTNAME_VERIFIER);
        this.scheme = new Scheme("https", 443, (SchemeSocketFactory)this.sslFactory);
        this.factory = new HttpComponentsClientHttpRequestFactory();
        this.factory.getHttpClient().getConnectionManager().getSchemeRegistry().register(this.scheme);
        return new RestTemplate((ClientHttpRequestFactory)this.factory);
    }
}

