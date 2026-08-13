/*
 * Decompiled with CFR 0.152.
 */
package fungsi;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;

public class ApiBPJSLZString {
    private static char[] keyStrBase64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=".toCharArray();
    private static char[] keyStrUriSafe = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+-$".toCharArray();
    private static Map<char[], Map<Character, Integer>> baseReverseDic = new HashMap<char[], Map<Character, Integer>>();

    private static char getBaseValue(char[] alphabet, Character character) {
        Map<Character, Integer> map = baseReverseDic.get(alphabet);
        if (map == null) {
            map = new HashMap<Character, Integer>();
            baseReverseDic.put(alphabet, map);
            for (int i = 0; i < alphabet.length; ++i) {
                map.put(Character.valueOf(alphabet[i]), i);
            }
        }
        return (char)map.get(character).intValue();
    }

    public static String compressToBase64(String input) {
        if (input == null) {
            return "";
        }
        String res = ApiBPJSLZString._compress(input, 6, new CompressFunctionWrapper(){

            @Override
            public char doFunc(int a) {
                return keyStrBase64[a];
            }
        });
        switch (res.length() % 4) {
            default: {
                return res;
            }
            case 1: {
                return res + "===";
            }
            case 2: {
                return res + "==";
            }
            case 3: 
        }
        return res + "=";
    }

    public static String decompressFromBase64(final String inputStr) {
        if (inputStr == null) {
            return "";
        }
        if (inputStr.equals("")) {
            return null;
        }
        return ApiBPJSLZString._decompress(inputStr.length(), 32, new DecompressFunctionWrapper(){

            @Override
            public char doFunc(int index) {
                return ApiBPJSLZString.getBaseValue(keyStrBase64, Character.valueOf(inputStr.charAt(index)));
            }
        });
    }

    public static String compressToUTF16(String input) {
        if (input == null) {
            return "";
        }
        return ApiBPJSLZString._compress(input, 15, new CompressFunctionWrapper(){

            @Override
            public char doFunc(int a) {
                return ApiBPJSLZString.fc(a + 32);
            }
        }) + " ";
    }

    public static String decompressFromUTF16(final String compressedStr) {
        if (compressedStr == null) {
            return "";
        }
        if (compressedStr.isEmpty()) {
            return null;
        }
        return ApiBPJSLZString._decompress(compressedStr.length(), 16384, new DecompressFunctionWrapper(){

            @Override
            public char doFunc(int index) {
                return (char)(compressedStr.charAt(index) - 32);
            }
        });
    }

    public static String compressToEncodedURIComponent(String input) {
        if (input == null) {
            return "";
        }
        return ApiBPJSLZString._compress(input, 6, new CompressFunctionWrapper(){

            @Override
            public char doFunc(int a) {
                return keyStrUriSafe[a];
            }
        });
    }

    public static String decompressFromEncodedURIComponent(String inputStr) {
        if (inputStr == null) {
            return "";
        }
        if (inputStr.isEmpty()) {
            return null;
        }
        final String urlEncodedInputStr = inputStr.replace(' ', '+');
        return ApiBPJSLZString._decompress(urlEncodedInputStr.length(), 32, new DecompressFunctionWrapper(){

            @Override
            public char doFunc(int index) {
                return ApiBPJSLZString.getBaseValue(keyStrUriSafe, Character.valueOf(urlEncodedInputStr.charAt(index)));
            }
        });
    }

    public static String compress(String uncompressed) {
        return ApiBPJSLZString._compress(uncompressed, 16, new CompressFunctionWrapper(){

            @Override
            public char doFunc(int a) {
                return ApiBPJSLZString.fc(a);
            }
        });
    }

    private static String _compress(String uncompressedStr, int bitsPerChar, CompressFunctionWrapper getCharFromInt) {
        int value;
        int i;
        if (uncompressedStr == null) {
            return "";
        }
        HashMap<String, Integer> context_dictionary = new HashMap<String, Integer>();
        HashSet<String> context_dictionaryToCreate = new HashSet<String>();
        String context_c = "";
        String context_wc = "";
        String context_w = "";
        int context_enlargeIn = 2;
        int context_dictSize = 3;
        int context_numBits = 2;
        StringBuilder context_data = new StringBuilder(uncompressedStr.length() / 3);
        int context_data_val = 0;
        int context_data_position = 0;
        for (int ii = 0; ii < uncompressedStr.length(); ++ii) {
            context_c = String.valueOf(uncompressedStr.charAt(ii));
            if (!context_dictionary.containsKey(context_c)) {
                context_dictionary.put(context_c, context_dictSize++);
                context_dictionaryToCreate.add(context_c);
            }
            if (context_dictionary.containsKey(context_wc = context_w + context_c)) {
                context_w = context_wc;
                continue;
            }
            if (context_dictionaryToCreate.contains(context_w)) {
                if (context_w.charAt(0) < '\u0100') {
                    for (i = 0; i < context_numBits; ++i) {
                        context_data_val <<= 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                            continue;
                        }
                        ++context_data_position;
                    }
                    value = context_w.charAt(0);
                    for (i = 0; i < 8; ++i) {
                        context_data_val = context_data_val << 1 | value & 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value >>= 1;
                    }
                } else {
                    value = 1;
                    for (i = 0; i < context_numBits; ++i) {
                        context_data_val = context_data_val << 1 | value;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value = 0;
                    }
                    value = context_w.charAt(0);
                    for (i = 0; i < 16; ++i) {
                        context_data_val = context_data_val << 1 | value & 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value >>= 1;
                    }
                }
                if (--context_enlargeIn == 0) {
                    context_enlargeIn = ApiBPJSLZString.powerOf2(context_numBits);
                    ++context_numBits;
                }
                context_dictionaryToCreate.remove(context_w);
            } else {
                value = (Integer)context_dictionary.get(context_w);
                for (i = 0; i < context_numBits; ++i) {
                    context_data_val = context_data_val << 1 | value & 1;
                    if (context_data_position == bitsPerChar - 1) {
                        context_data_position = 0;
                        context_data.append(getCharFromInt.doFunc(context_data_val));
                        context_data_val = 0;
                    } else {
                        ++context_data_position;
                    }
                    value >>= 1;
                }
            }
            if (--context_enlargeIn == 0) {
                context_enlargeIn = ApiBPJSLZString.powerOf2(context_numBits);
                ++context_numBits;
            }
            context_dictionary.put(context_wc, context_dictSize++);
            context_w = context_c;
        }
        if (!context_w.isEmpty()) {
            if (context_dictionaryToCreate.contains(context_w)) {
                if (context_w.charAt(0) < '\u0100') {
                    for (i = 0; i < context_numBits; ++i) {
                        context_data_val <<= 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                            continue;
                        }
                        ++context_data_position;
                    }
                    value = context_w.charAt(0);
                    for (i = 0; i < 8; ++i) {
                        context_data_val = context_data_val << 1 | value & 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value >>= 1;
                    }
                } else {
                    value = 1;
                    for (i = 0; i < context_numBits; ++i) {
                        context_data_val = context_data_val << 1 | value;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value = 0;
                    }
                    value = context_w.charAt(0);
                    for (i = 0; i < 16; ++i) {
                        context_data_val = context_data_val << 1 | value & 1;
                        if (context_data_position == bitsPerChar - 1) {
                            context_data_position = 0;
                            context_data.append(getCharFromInt.doFunc(context_data_val));
                            context_data_val = 0;
                        } else {
                            ++context_data_position;
                        }
                        value >>= 1;
                    }
                }
                if (--context_enlargeIn == 0) {
                    context_enlargeIn = ApiBPJSLZString.powerOf2(context_numBits);
                    ++context_numBits;
                }
                context_dictionaryToCreate.remove(context_w);
            } else {
                value = (Integer)context_dictionary.get(context_w);
                for (i = 0; i < context_numBits; ++i) {
                    context_data_val = context_data_val << 1 | value & 1;
                    if (context_data_position == bitsPerChar - 1) {
                        context_data_position = 0;
                        context_data.append(getCharFromInt.doFunc(context_data_val));
                        context_data_val = 0;
                    } else {
                        ++context_data_position;
                    }
                    value >>= 1;
                }
            }
            if (--context_enlargeIn == 0) {
                context_enlargeIn = ApiBPJSLZString.powerOf2(context_numBits);
                ++context_numBits;
            }
        }
        value = 2;
        for (i = 0; i < context_numBits; ++i) {
            context_data_val = context_data_val << 1 | value & 1;
            if (context_data_position == bitsPerChar - 1) {
                context_data_position = 0;
                context_data.append(getCharFromInt.doFunc(context_data_val));
                context_data_val = 0;
            } else {
                ++context_data_position;
            }
            value >>= 1;
        }
        while (true) {
            context_data_val <<= 1;
            if (context_data_position == bitsPerChar - 1) break;
            ++context_data_position;
        }
        context_data.append(getCharFromInt.doFunc(context_data_val));
        return context_data.toString();
    }

    public static String f(int i) {
        return String.valueOf((char)i);
    }

    public static char fc(int i) {
        return (char)i;
    }

    public static String decompress(final String compressed) {
        if (compressed == null) {
            return "";
        }
        if (compressed.isEmpty()) {
            return null;
        }
        return ApiBPJSLZString._decompress(compressed.length(), 32768, new DecompressFunctionWrapper(){

            @Override
            public char doFunc(int i) {
                return compressed.charAt(i);
            }
        });
    }

    private static String _decompress(int length, int resetValue, DecompressFunctionWrapper getNextValue) {
        int resb;
        int power;
        ArrayList<String> dictionary = new ArrayList<String>();
        int enlargeIn = 4;
        int dictSize = 4;
        int numBits = 3;
        String entry = "";
        StringBuilder result = new StringBuilder();
        String c = null;
        DecData data = new DecData();
        data.val = getNextValue.doFunc(0);
        data.position = resetValue;
        data.index = 1;
        for (int i = 0; i < 3; ++i) {
            dictionary.add(i, ApiBPJSLZString.f(i));
        }
        int bits = 0;
        int maxpower = ApiBPJSLZString.powerOf2(2);
        for (power = 1; power != maxpower; power <<= 1) {
            resb = data.val & data.position;
            data.position >>= 1;
            if (data.position == 0) {
                data.position = resetValue;
                data.val = getNextValue.doFunc(data.index++);
            }
            bits |= (resb > 0 ? 1 : 0) * power;
        }
        int next = bits;
        switch (next) {
            case 0: {
                bits = 0;
                maxpower = ApiBPJSLZString.powerOf2(8);
                for (power = 1; power != maxpower; power <<= 1) {
                    resb = data.val & data.position;
                    data.position >>= 1;
                    if (data.position == 0) {
                        data.position = resetValue;
                        data.val = getNextValue.doFunc(data.index++);
                    }
                    bits |= (resb > 0 ? 1 : 0) * power;
                }
                c = ApiBPJSLZString.f(bits);
                break;
            }
            case 1: {
                bits = 0;
                maxpower = ApiBPJSLZString.powerOf2(16);
                for (power = 1; power != maxpower; power <<= 1) {
                    resb = data.val & data.position;
                    data.position >>= 1;
                    if (data.position == 0) {
                        data.position = resetValue;
                        data.val = getNextValue.doFunc(data.index++);
                    }
                    bits |= (resb > 0 ? 1 : 0) * power;
                }
                c = ApiBPJSLZString.f(bits);
                break;
            }
            case 2: {
                return "";
            }
        }
        dictionary.add(3, c);
        String w = c;
        result.append(w);
        while (data.index <= length) {
            bits = 0;
            maxpower = ApiBPJSLZString.powerOf2(numBits);
            for (power = 1; power != maxpower; power <<= 1) {
                resb = data.val & data.position;
                data.position >>= 1;
                if (data.position == 0) {
                    data.position = resetValue;
                    data.val = getNextValue.doFunc(data.index++);
                }
                bits |= (resb > 0 ? 1 : 0) * power;
            }
            int cc = bits;
            switch (cc) {
                case 0: {
                    bits = 0;
                    maxpower = ApiBPJSLZString.powerOf2(8);
                    for (power = 1; power != maxpower; power <<= 1) {
                        resb = data.val & data.position;
                        data.position >>= 1;
                        if (data.position == 0) {
                            data.position = resetValue;
                            data.val = getNextValue.doFunc(data.index++);
                        }
                        bits |= (resb > 0 ? 1 : 0) * power;
                    }
                    dictionary.add(dictSize++, ApiBPJSLZString.f(bits));
                    cc = dictSize - 1;
                    --enlargeIn;
                    break;
                }
                case 1: {
                    bits = 0;
                    maxpower = ApiBPJSLZString.powerOf2(16);
                    for (power = 1; power != maxpower; power <<= 1) {
                        resb = data.val & data.position;
                        data.position >>= 1;
                        if (data.position == 0) {
                            data.position = resetValue;
                            data.val = getNextValue.doFunc(data.index++);
                        }
                        bits |= (resb > 0 ? 1 : 0) * power;
                    }
                    dictionary.add(dictSize++, ApiBPJSLZString.f(bits));
                    cc = dictSize - 1;
                    --enlargeIn;
                    break;
                }
                case 2: {
                    return result.toString();
                }
            }
            if (enlargeIn == 0) {
                enlargeIn = ApiBPJSLZString.powerOf2(numBits);
                ++numBits;
            }
            if (cc < dictionary.size() && dictionary.get(cc) != null) {
                entry = (String)dictionary.get(cc);
            } else if (cc == dictSize) {
                entry = w + w.charAt(0);
            } else {
                return null;
            }
            result.append(entry);
            dictionary.add(dictSize++, w + entry.charAt(0));
            w = entry;
            if (--enlargeIn != 0) continue;
            enlargeIn = ApiBPJSLZString.powerOf2(numBits);
            ++numBits;
        }
        return "";
    }

    private static int powerOf2(int power) {
        return 1 << power;
    }

    private static abstract class CompressFunctionWrapper {
        private CompressFunctionWrapper() {
        }

        public abstract char doFunc(int var1);
    }

    private static abstract class DecompressFunctionWrapper {
        private DecompressFunctionWrapper() {
        }

        public abstract char doFunc(int var1);
    }

    protected static class DecData {
        public char val;
        public int position;
        public int index;

        protected DecData() {
        }
    }
}

