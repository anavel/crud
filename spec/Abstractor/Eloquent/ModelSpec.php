<?php

namespace spec\ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;

class ModelSpec extends ObjectBehavior
{
    public function let(ModelManager $modelManager)
    {
        $config = require __DIR__.'/../../config.php';

        $this->beConstructedWith($config, $modelManager);
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
}
