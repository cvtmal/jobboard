<?php

declare (strict_types=1);
namespace Rector\VersionBonding;

use Rector\Contract\Rector\RectorInterface;
use Rector\Php\PhpVersionProvider;
use Rector\Php\PolyfillPackagesProvider;
use Rector\VersionBonding\Contract\MinPhpVersionInterface;
use Rector\VersionBonding\Contract\RelatedPolyfillInterface;
final class PhpVersionedFilter
{
    /**
     * @readonly
     */
    private PhpVersionProvider $phpVersionProvider;
    /**
     * @readonly
     */
    private PolyfillPackagesProvider $polyfillPackagesProvider;
    public function __construct(PhpVersionProvider $phpVersionProvider, PolyfillPackagesProvider $polyfillPackagesProvider)
    {
        $this->phpVersionProvider = $phpVersionProvider;
        $this->polyfillPackagesProvider = $polyfillPackagesProvider;
    }
    /**
     * @param list<RectorInterface> $rectors
     * @return list<RectorInterface>
     */
    public function filter(array $rectors) : array
    {
        $minProjectPhpVersion = $this->phpVersionProvider->provide();
        $activeRectors = [];
        foreach ($rectors as $rector) {
            if ($rector instanceof RelatedPolyfillInterface) {
                $polyfillPackageNames = $this->polyfillPackagesProvider->provide();
                if (\in_array($rector->providePolyfillPackage(), $polyfillPackageNames, \true)) {
                    $activeRectors[] = $rector;
                    continue;
                }
            }
            if (!$rector instanceof MinPhpVersionInterface) {
                $activeRectors[] = $rector;
                continue;
            }
            // does satisfy version? → include
            if ($rector->provideMinPhpVersion() <= $minProjectPhpVersion) {
                $activeRectors[] = $rector;
            }
        }
        return $activeRectors;
    }
}
