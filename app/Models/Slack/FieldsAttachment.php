<?php

declare(strict_types=1);

namespace App\Models\Slack;

/**
 * Fields attachment for Slack.
 */
class FieldsAttachment extends Attachment
{
    /**
     * Fields to include with the attachment.
     * @var array<int, array<string, bool|string>>
     */
    protected array $fields = [];

    /**
     * Constructor.
     * @param string $title
     */
    public function __construct(protected string $title)
    {
    }

    /**
     * Add a field to the attachment.
     * @param Field $field
     * @return FieldsAttachment
     */
    public function addField(Field $field): FieldsAttachment
    {
        $this->fields[] = $field->toArray();
        return $this;
    }

    /**
     * Return the attachment as an array.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'fields' => $this->fields,
        ];
    }
}
