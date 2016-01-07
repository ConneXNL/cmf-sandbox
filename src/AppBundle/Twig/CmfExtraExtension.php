<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Twig;
use Symfony\Cmf\Bundle\CoreBundle\Templating\Helper\CmfHelper;
use Symfony\Cmf\Bundle\SeoBundle\SeoPresentationInterface;

class CmfExtraExtension extends \Twig_Extension
{

    /**
     * @var CmfHelper
     */
    private $helper;

    /**
     * @var string
     */
    private $defaultContentPath;

    /**
     * @var SeoPresentationInterface
     */
    protected $seoPresentation;

    public function __construct(CmfHelper $helper, $defaultContentPath = '/cms/content/', SeoPresentationInterface $seoPresentation = null)
    {
        $this->helper = $helper;
        $this->defaultContentPath = $defaultContentPath;
        $this->seoPresentation = $seoPresentation;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cmf_find_current_published_content', array($this, 'findCurrentPublishedCMFContent')),
        );
    }

    /**
    * Example usage: {% set cmfMainContent = cmf_find_current_published_content(['acme_message/inbox', 'acme_message']) %}
    * This example will try to find a content at /cms/content/acme_message/inbox, update the SEO Meta data and stores
    * the content document in the cmfMainContent variable. Using 
    *     {% block html_title %}{{ sonata_seo_title() }}{% endblock html_title %}
    *     {% block html_metadatas %}{{ sonata_seo_metadatas() }}{% endblock html_metadatas %}
    * in a parent will apply the seo meta data from the page.
    * */
    public function findCurrentPublishedCMFContent($optionalContentNames){
        if (!is_array($optionalContentNames)) $optionalContentNames = array($optionalContentNames);
        $match = null;

        foreach ($optionalContentNames as $contentName){
            $contentPath = substr($contentName, 0, 1) !== '/' ? $this->defaultContentPath."/".$contentName : $contentName;
            $content = $this->helper->find($contentPath);
            if ($content && $this->helper->isPublished($content)){
                $this->seoPresentation->updateSeoPage($content);
                $match = $content;
                break;
            }
        }

        return $match;
    }

    public function getName()
    {
        return 'cmf_extra';
    }
}
