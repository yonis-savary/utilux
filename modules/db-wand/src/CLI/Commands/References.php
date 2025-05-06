<?php 

namespace YonisSavary\DBWand\CLI\Commands;

use YonisSavary\DBWand\CLI\Command;
use YonisSavary\DBWand\Context;
use YonisSavary\DBWand\Database\Classes\Field;
use YonisSavary\DBWand\Database\Classes\ForeignKey;

class References extends Command
{
    public function name(): string
    {
        return "references";
    }

    public function help(): ?string
    {
        return "Select every constraint referencing a column from a table";
    }

    public function execute(array $argv, Context &$context): bool
    {
        if (! $table = $argv[0] ?? null)
            return $this->failWithReason("This command needs a table name and a field name");

        if (! $column = $argv[1] ?? null)
            return $this->failWithReason("This command needs a table name and a field name");

        $constraints = $context->utils->getFieldsPointingTo(new Field($table, $column));

        $context->dataset = array_map(fn(ForeignKey $constraint)=> [
            "source_table" => $constraint->source->table,
            "source_field" => $constraint->source->field,
            "target_table" => $constraint->target->table,
            "target_field" => $constraint->target->field,
        ], $constraints);

        Backup::add($context);
        
        $command = $context->terminal->getCommand("show");
        $command->execute(["0-". (count($constraints)+1)], $context);

        return true;
    }
}