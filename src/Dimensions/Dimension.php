<?php
declare(strict_types=1);

namespace Level23\Druid\Dimensions;

use InvalidArgumentException;
use Level23\Druid\Types\DataType;
use Level23\Druid\Extractions\ExtractionInterface;

class Dimension implements DimensionInterface
{
    protected string $dimension;

    protected string $outputName;

    protected DataType $outputType;

    protected ?ExtractionInterface $extractionFunction;

    /**
     * Dimension constructor.
     *
     * @param string                   $dimension
     * @param string|null              $outputName
     * @param string|DataType          $outputType This can either be "long", "float" or "string"
     * @param ExtractionInterface|null $extractionFunction
     */
    public function __construct(
        string $dimension,
        string $outputName = null,
        string|DataType $outputType = DataType::STRING,
        ExtractionInterface $extractionFunction = null
    ) {
        $this->dimension  = $dimension;
        $this->outputName = $outputName ?: $dimension;

        if( empty($outputType)) {
            $outputType = DataType::STRING;
        } else {
            $outputType = is_string($outputType) ? DataType::from(strtolower($outputType)) : $outputType;
        }

        if (!in_array($outputType, [DataType::STRING, DataType::LONG, DataType::FLOAT])) {
            throw new InvalidArgumentException(
                'Incorrect type given: ' . $outputType->value . '. This can either be "long", "float" or "string"'
            );
        }

        $this->outputType         = $outputType;
        $this->extractionFunction = $extractionFunction;
    }

    /**
     * Return the dimension as it should be used in a druid query.
     *
     * @return array<string,string|array<mixed>>
     */
    public function toArray(): array
    {
        $result = [
            'type'       => ($this->extractionFunction ? 'extraction' : 'default'),
            'dimension'  => $this->dimension,
            'outputType' => $this->outputType->value,
            'outputName' => $this->outputName,
        ];

        if ($this->extractionFunction) {
            $result['extractionFn'] = $this->extractionFunction->toArray();
        }

        return $result;
    }

    /**
     * Return the name of the dimension which is selected.
     *
     * @return string
     */
    public function getDimension(): string
    {
        return $this->dimension;
    }

    /**
     * Return the output name of this dimension
     *
     * @return string
     */
    public function getOutputName(): string
    {
        return $this->outputName;
    }

    /**
     * @return \Level23\Druid\Extractions\ExtractionInterface|null
     */
    public function getExtractionFunction(): ?ExtractionInterface
    {
        return $this->extractionFunction;
    }
}