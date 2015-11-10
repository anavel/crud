<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Translation;
use ANavallaSuiza\Laravel\Database\Manager\Eloquent\ModelManager;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;

class TranslationTest extends TestBase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->model = new Translation(
            Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            new User(),
            $config['Users']['relations']['translations']['name'],
            $config['Users']['relations']['translations']['presentation']
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->model);
    }

    public function test_returns_edit_fields_as_array()
    {
        $fields = $this->model->getEditFields();

        $this->assertInternalType('array', $fields);
    }
}
