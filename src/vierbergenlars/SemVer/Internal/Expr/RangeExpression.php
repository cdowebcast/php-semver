<?php

namespace vierbergenlars\SemVer\Internal\Expr;

use vierbergenlars\SemVer\Internal\AbstractVersion;
use vierbergenlars\SemVer\Internal\Version;
class RangeExpression implements ExpressionInterface
{
    private $start;
    private $end;
    private $normalized;

    public function __construct(AbstractVersion $start, AbstractVersion $end)
    {
        $this->start = $start->unsetToZero();
        $this->end = $end;

        $endexpr = new LessThanExpression($this->end->unsetToZero());
        if($this->end->getPatch() === null) {
            $endexpr = new LessThanExpression($this->end->increment(Version::MINOR));
        }
        if($this->end->getMinor() === null) {
            $endexpr = new LessThanExpression($this->end->increment(Version::MAJOR));
        }
        if($this->end->getMajor() === null) {
            $endexpr = new AnyExpression();
        }

        $expr = new AndExpression(array(
            new GreaterThanOrEqualExpression($this->start),
            $endexpr,
        ));

        $this->normalized = $expr;
    }

    public function matches(AbstractVersion $version)
    {
        return $this->normalized->matches($version);
    }

    public function __toString()
    {
        return $this->start.' - '.$this->end;
    }
    public function getNormalized()
    {
        return $this->normalized->getNormalized();
    }

}