<?php

namespace Test;

use App\Facades\Crypt;
use App\Facades\Hash;

class UtilsTest extends TestCase
{
    public function testHash(): void
    {
        $hashed_value = Hash::make('This is a hash test');
        $result_true = Hash::check('This is a hash test', $hashed_value);
        $result_false = Hash::check('This is a wrong hash test', $hashed_value);
        $this->assertTrue($result_true);
        $this->assertFalse($result_false);
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
