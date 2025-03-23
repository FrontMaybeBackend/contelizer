<?php

use App\Service\PeselValidator;
use PHPUnit\Framework\TestCase;

class PeselValidatorTest extends TestCase
{
    private PeselValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PeselValidator();
    }

    public function testValidPesel(): void
    {
        $this->assertTrue($this->validator->validate('44051401458'));
    }

    public function testInvalidLength(): void
    {
        $this->assertFalse($this->validator->validate('4405140145'));
        $this->assertFalse($this->validator->validate('440514014589'));
    }

    public function testInvalidCharacters(): void
    {
        $this->assertFalse($this->validator->validate('4405140145A'));
    }

    public function testInvalidChecksum(): void
    {
        $this->assertFalse($this->validator->validate('44051401459'));
    }

    public function testValidBirthDate(): void
    {
        $this->assertTrue($this->validator->validateBirthDate('44051401458'));
    }

    public function testInvalidBirthDate(): void
    {
        // Niepoprawna data (31 kwietnia)
        $this->assertFalse($this->validator->validateBirthDate('44043101450'));
    }

}
