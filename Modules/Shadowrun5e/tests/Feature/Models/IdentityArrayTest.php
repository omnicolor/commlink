<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Identity;
use Modules\Shadowrun5e\Models\IdentityArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class IdentityArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var IdentityArray<Identity>
     */
    protected IdentityArray $identities;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->identities = new IdentityArray();
    }

    /**
     * Test an empty IdentityArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->identities);
    }

    /**
     * Test adding an identity to the array.
     */
    public function testAdd(): void
    {
        $this->identities[] = new Identity();
        self::assertNotEmpty($this->identities);
    }

    /**
     * Test that adding a non-identity to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        self::expectExceptionMessage(
            'IdentityArray only accepts Identity objects'
        );
        // @phpstan-ignore offsetAssign.valueType
        $this->identities[] = new stdClass();
    }

    /**
     * Test that adding a non-identity to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->identities->offsetSet(identity: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->identities);
    }
}
