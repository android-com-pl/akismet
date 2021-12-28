<?php

/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Flarum\Akismet\Listener;

use Flarum\Akismet\Akismet;
use Flarum\Post\Event\Hidden;

class SubmitSpam
{
    /**
     * @var Akismet
     */
    protected $akismet;

    public function __construct(Akismet $akismet)
    {
        $this->akismet = $akismet;
    }

    public function handle(Hidden $event)
    {
        if (!$this->akismet->isConfigured) {
            return;
        }

        $post = $event->post;

        if ($post->is_spam) {
            $this->akismet
                ->setContent($post->content)
                ->setIp($post->ip_address)
                ->setAuthorName($post->user->username)
                ->setAuthorEmail($post->user->email)
                ->setType($post->number === 1 ? 'forum-post' : 'reply');

            $this->akismet->submitSpam();
        }
    }
}
