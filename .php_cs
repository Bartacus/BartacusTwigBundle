<?php

declare(strict_types=1);

/*
 * This file is part of the Bartacus Twig bundle.
 *
 * Copyright (c) Emily Karisch
 *
 * This bundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This bundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this bundle. If not, see <http://www.gnu.org/licenses/>.
 */

$commonFinder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('public')
    ->in(__DIR__)
;

$header = <<<'EOF'
This file is part of the Bartacus Twig bundle.

Copyright (c) Emily Karisch

This bundle is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This bundle is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this bundle. If not, see <http://www.gnu.org/licenses/>.
EOF;

$commonRules = [
    '@Symfony' => true,
    '@Symfony:risky' => true,
    '@PHP71Migration' => true,
    '@PHP71Migration:risky' => true,
    '@DoctrineAnnotation' => true,
    'align_multiline_comment' => true,
    'array_indentation' => true,
    'array_syntax' => ['syntax' => 'short'],
    'backtick_to_shell_exec' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'compact_nullable_typehint' => true,
    'date_time_immutable' => true,
    'escape_implicit_backslashes' => [
        'double_quoted' => true,
        'heredoc_syntax' => true,
        'single_quoted' => true,
    ],
    'explicit_indirect_variable' => true,
    'explicit_string_variable' => true,
    'final_internal_class' => true,
    'fully_qualified_strict_types' => true,
    'general_phpdoc_annotation_remove' => ['author', 'package', 'subpackage'],
    'header_comment' => ['header' => $header],
    'heredoc_to_nowdoc' => true,
    'linebreak_after_opening_tag' => true,
    'list_syntax' => ['syntax' => 'short'],
    'logical_operators' => true,
    'mb_str_functions' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'new_line_for_chained_calls',
    ],
    'no_alternative_syntax' => true,
    'no_binary_string' => true,
    'no_null_property_initialization' => true,
    'no_php4_constructor' => true,
    'no_superfluous_elseif' => true,
    'no_superfluous_phpdoc_tags' => true,
    'no_unneeded_curly_braces' => true,
    'no_unneeded_final_method' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unset_cast' => true,
    'no_unset_on_property' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'ordered_class_elements' => true,
    'ordered_imports' => true,
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_order' => true,
    'phpdoc_to_return_type' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'none',
    ],
    'phpdoc_var_annotation_correct_order' => true,
    'return_assignment' => true,
    'static_lambda' => true,
    'strict_comparison' => true,
    'strict_param' => true,
    'string_line_ending' => true,
];

$finder = clone $commonFinder;
$finder
    // no declare and use statements allowed, because of cache concatenation
    // so we have to ignore thus files and check them separately.
    ->notPath('ext_localconf.php')
    ->notPath('ext_tables.php')
;

return PhpCsFixer\Config::create()
    ->setRules($commonRules)
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
;
