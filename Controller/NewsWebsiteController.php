<?php

declare(strict_types=1);

/*
 * This file is based on that of TheCadien/SuluNewsBundle.
 *
 * Original version (c) Oliver Kossin
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 * This is an override to make the website controller match the
 * Twig naming convention used in other parts of the site (and
 * other sites). It is mainly used for the preview in the admin
 * area as I use a custom controller for the front end.
 */

namespace TheCadien\Bundle\SuluNewsBundle\Controller;

use Sulu\Bundle\PreviewBundle\Preview\Preview;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TheCadien\Bundle\SuluNewsBundle\Entity\News;

/**
 * Class NewsWebsiteController.
 */
class NewsWebsiteController extends AbstractController
{
    public function indexAction(News $news, $attributes = [], $preview = false, $partial = false): Response
    {
        if (!$news) {
            throw new NotFoundHttpException();
        }

        if ($partial) {
            $content = $this->renderBlock(
                'news/show.html.twig',
                'content',
                ['news' => $news]
            );
        } elseif ($preview) {
            $content = $this->renderPreview(
                'news/show.html.twig',
                ['news' => $news]
            );
        } else {
            $content = $this->renderView(
                'news/show.html.twig',
                ['news' => $news]
            );
        }

        return new Response($content);
    }

    protected function renderPreview(string $view, array $parameters = []): string
    {
        $parameters['previewParentTemplate'] = $view;
        $parameters['previewContentReplacer'] = Preview::CONTENT_REPLACER;

        return $this->renderView('@SuluWebsite/Preview/preview.html.twig', $parameters);
    }

    /**
     * Returns rendered part of template specified by block.
     *
     * @param mixed $template
     * @param mixed $block
     * @param mixed $attributes
     */
    protected function renderBlock($template, $block, $attributes = [])
    {
        $twig = $this->container->get('twig');
        $attributes = $twig->mergeGlobals($attributes);

        $template = $twig->load($template);

        $level = ob_get_level();
        ob_start();

        try {
            $rendered = $template->renderBlock($block, $attributes);
            ob_end_clean();

            return $rendered;
        } catch (\Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }
    }
}
