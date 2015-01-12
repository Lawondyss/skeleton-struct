<?php
/**
 * Class BasePresenter
 * @author Ladislav Vondráček
 */

namespace Core;

use CDNLoader\CDNLoaderFactory;
use Nette\Application\UI\Presenter;
use Kdyby\Autowired AS KA;
use WebLoader\Nette\LoaderFactory AS WLoaderFactory;
use Tracy\Debugger;

abstract class BasePresenter extends Presenter
{
    use KA\AutowireProperties;
    use KA\AutowireComponentFactories;


    public function formatLayoutTemplateFiles()
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $dir = dirname(dirname($this->getReflection()->getFileName()));
        $layout = $this->layout ?: 'layout';

        $files = [
            $dir . '/' . $presenter . '/@' . $layout . '.latte',
            $dir . '/@' . $layout . '.latte',
            $this->context->parameters['appDir'] . '/modules/core/@' . $layout . '.latte',
        ];

        return $files;
    }


    public function formatTemplateFiles()
    {
        $name = $this->getName();
        $presenter = substr($name, strrpos(':' . $name, ':'));
        $dir = dirname(dirname($this->getReflection()->getFileName()));

        $files = [
            $dir . '/' . $presenter . '/templates/' . $this->getView() . '.latte',
        ];

        return $files;
    }


    protected function beforeRender()
    {
        $this->template->title = $this->getAppParameter('title');
        $this->template->description = $this->getAppParameter('description');
        $this->template->keywords = implode(', ', $this->getAppParameter('keywords'));
    }


    /**
     * Return value of parameter in "app" index
     * For a deeper determination use dot notation: [key1 => [key2 => [key3 => value]]] == key1.key2.key3
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function getAppParameter($name)
    {
        $values = $this->context->parameters['app'];
        $nextIndex = $name;

        // explode by "." in name to indexes
        while (strpos($nextIndex, '.') !== false) {
            $index = strstr($nextIndex, '.', true);
            $nextIndex = substr(strstr($nextIndex, '.'), 1);

            if (!array_key_exists($index, $values)) {
                $msg = sprintf('Parameter "%s" not exists.', $name);
                throw new \InvalidArgumentException($msg);
            }

            $values = $values[$index];
        }

        $name = $nextIndex;

        if (!array_key_exists($name, $values)) {
            $msg = sprintf('Parameter "%s" not exists.', $name);
            throw new \InvalidArgumentException($msg);
        }

        return $values[$name];
    }


    protected function createComponentCssScreen(WLoaderFactory $webloader)
    {
        $control = $webloader->createCssLoader('screen');
        $control->setMedia('screen,projection,tv');

        return $control;
    }


    protected function createComponentCssPrint(WLoaderFactory $webloader)
    {
        $control = $webloader->createCssLoader('print');
        $control->setMedia('print');

        return $control;
    }


    protected function createComponentCdnLoader(CDNLoaderFactory $factory)
    {
        $control = $factory->create();
        return $control;
    }
}
