<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\SourceLink;

interface SourceLinkRepository
{
    public function persist(SourceLink $element);
    public function flush(SourceLink $element = null);
    public function remove(SourceLink $element);
}
