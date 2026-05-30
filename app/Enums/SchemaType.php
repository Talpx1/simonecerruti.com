<?php

declare(strict_types=1);

namespace App\Enums;

enum SchemaType: string {
    case ARTICLE = 'article';
    case BLOG_POSTING = 'blog_posting';
    case CREATIVE_WORK = 'creative_work';
    case WEB_PAGE = 'web_page';
    case WEB_SITE = 'web_site';
    case PERSON = 'person';
    case ORGANIZATION = 'organization';
    case PRODUCT = 'product';
    case FAQ_PAGE = 'faq_page';
    case BREADCRUMB_LIST = 'breadcrumb_list';

    /**
     * The canonical schema.org `@type` identifier emitted in JSON-LD.
     */
    public function schemaOrgType(): string {
        return match ($this) {
            self::ARTICLE => 'Article',
            self::BLOG_POSTING => 'BlogPosting',
            self::CREATIVE_WORK => 'CreativeWork',
            self::WEB_PAGE => 'WebPage',
            self::WEB_SITE => 'WebSite',
            self::PERSON => 'Person',
            self::ORGANIZATION => 'Organization',
            self::PRODUCT => 'Product',
            self::FAQ_PAGE => 'FAQPage',
            self::BREADCRUMB_LIST => 'BreadcrumbList',
        };
    }
}
