<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Models\User;
use Illuminate\Console\Command;
use App\Exceptions\InvalidTokenException;
use Illuminate\Support\Facades\Storage;

class SitemapGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     *
     * @throws \App\Exceptions\InvalidTokenException
     */
    public function handle()
    {
        $chunkSize = count(config('app.locales')) + 1;
        $chunks = User::select('tag', 'nickname', 'updated_at')
            ->get()
            ->chunk(floor(50e3 / $chunkSize));

        $count = $chunks->count();

        $index = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex />');
        $index->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        for ($i = 1; $i <= $count; $i++)
        {
            $this->generateChunk($i, $chunks->get($i - 1));

            $subSitemap = $index->addChild('sitemap');
            $subSitemap->addChild('loc', 'https://' . config('app.domain') . '/users' . $i . '.xml');
        }

        Storage::put('sitemap/sitemap.xml', $index->asXML());
    }

    /**
     * @param int $n
     * @param $users
     */
    protected function generateChunk($n = 1, $users)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset />');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->addAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        $users->map(function ($user) use ($xml) {
            $this->getUserUrl($user, $xml->addChild('url'));
        });

        Storage::put('sitemap/users' . $n . '.xml', $xml->asXML());
    }

    /**
     * @param $user
     * @param \SimpleXMLElement $node
     */
    protected function getUserUrl($user, \SimpleXMLElement $node)
    {
        $node->addChild('loc', $this->getUrl($user));
        $node->addChild('changefreq', 'monthly');
        $node->addChild('lastmod', $user->updated_at->format('Y-m-d'));

        foreach (config('app.locales') as $locale)
        {
            $link = $node->addChild('xhtml:link');

            $link->addAttribute('hreflang', $locale);
            $link->addAttribute('rel', 'alternate');
            $link->addAttribute('href', $this->getUrl($user, $locale));
        }
    }

    /**
     * @param $user
     * @param string $locale
     *
     * @return string
     */
    protected function getUrl($user, $locale = 'en')
    {
        $domain = config('app.domain');

        if ($locale !== 'en') {
            $domain = $locale . '.' . $domain;
        }

        return 'https://' . $domain . '/valorant/player/' . $user->nickname . '/' . $user->tag;
    }
}
