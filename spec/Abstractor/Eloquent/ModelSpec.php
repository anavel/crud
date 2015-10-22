<?php

namespace spec\ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer;

class ModelSpec extends ObjectBehavior
{
    protected $config;

    public function let(ModelManager $modelManager)
    {
        $this->config = require __DIR__.'/../../config.php';

        $this->beConstructedWith($this->config, $modelManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model');
    }

    public function it_implements_model_interface()
    {
        $this->shouldImplement('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model');
    }

    public function it_loads_model_by_slug()
    {
        $this->loadBySlug('users');

        $this->getSlug()->shouldReturn('users');
        $this->getName()->shouldReturn('Users');
        $this->getModel()->shouldReturn('App\User');
    }

    public function it_loads_model_by_name()
    {
        $this->loadByName('Blog Posts');

        $this->getSlug()->shouldReturn('blog-posts');
        $this->getName()->shouldReturn('Blog Posts');
        $this->getModel()->shouldReturn('App\Post');
    }

    public function it_returns_list_fields_as_array(ModelManager $modelManager, AbstractionLayer $dbal)
    {
        $dbal->getTableColumns()->willReturn(array());
        $modelManager->getAbstractionLayer('App\User')->willReturn($dbal);

        $this->beConstructedWith($this->config, $modelManager);

        $this->loadBySlug('users');

        $this->getListFields()->shouldBeArray();
    }

    public function it_returns_detail_fields_as_array(ModelManager $modelManager, AbstractionLayer $dbal)
    {
        $dbal->getTableColumns()->willReturn(array());
        $modelManager->getAbstractionLayer('App\User')->willReturn($dbal);

        $this->beConstructedWith($this->config, $modelManager);

        $this->loadBySlug('users');

        $this->getDetailFields()->shouldBeArray();
    }

    public function it_returns_edit_fields_as_array(ModelManager $modelManager, AbstractionLayer $dbal)
    {
        $dbal->getTableColumns()->willReturn(array());
        $modelManager->getAbstractionLayer('App\User')->willReturn($dbal);

        $this->beConstructedWith($this->config, $modelManager);

        $this->loadBySlug('users');

        $this->getEditFields()->shouldBeArray();
    }
}
