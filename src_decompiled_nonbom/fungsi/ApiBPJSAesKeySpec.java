/*
 * Decompiled with CFR 0.152.
 */
package fungsi;

import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;

public class ApiBPJSAesKeySpec {
    private SecretKeySpec key;
    private IvParameterSpec iv;

    public SecretKeySpec getKey() {
        return this.key;
    }

    public void setKey(SecretKeySpec key) {
        this.key = key;
    }

    public IvParameterSpec getIv() {
        return this.iv;
    }

    public void setIv(IvParameterSpec iv) {
        this.iv = iv;
    }
}

