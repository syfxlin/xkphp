<?php

namespace Test\Unit;

use App\Facades\Crypt;
use App\Facades\Hash;
use RuntimeException;
use Test\TestCase;
use function base64_decode;
use function base64_encode;
use function env;
use function json_decode;
use function json_encode;
use function sprintf_array;

class UtilsTest extends TestCase
{
    public function testHash(): void
    {
        $str = sprintf_array("This is a :str(s),but is a :int, :integer(d)", [
            'str' => 'string',
            'int' => 2,
            'integer' => 3,
        ]);
        $hashed_value = Hash::make('This is a hash test');
        $result_true = Hash::check('This is a hash test', $hashed_value);
        $result_false = Hash::check('This is a wrong hash test', $hashed_value);
        $this->assertTrue($result_true);
        $this->assertFalse($result_false);
        $this->assertIsBool(Hash::needsRehash($hashed_value));
    }

    public function testMakeCryptException(): void
    {
        $this->expectException(RuntimeException::class);
        $crypt = new \App\Utils\Crypt(
            base64_decode(env('APP_KEY')),
            'AES-128-CBC'
        );
    }

    public function testMacVaildException(): void
    {
        $this->expectException(RuntimeException::class);
        $encrypt_string = Crypt::encryptString('This is a crypt test');
        $encrypt_arr = json_decode(base64_decode($encrypt_string), true);
        $encrypt_arr['value'] = '';
        Crypt::decryptString(base64_encode(json_encode($encrypt_arr)));
    }

    public function testVaildCryptException(): void
    {
        $this->expectException(RuntimeException::class);
        Crypt::decryptString('');
    }

    public function testCrypt(): void
    {
        $key = openssl_random_pseudo_bytes(32);
        $origin_string = 'This is is a crypt test';
        $encrypt_string = Crypt::encryptString($origin_string);
        $decrypt_string = Crypt::decryptString($encrypt_string);
        $origin_data = [1, 2, 3];
        $encrypt_data = Crypt::encryptSerialize($origin_data);
        $decrypt_data = Crypt::decryptSerialize($encrypt_data);
        $this->assertEquals($origin_string, $decrypt_string);
        $this->assertEquals($origin_data, $decrypt_data);
    }
}
