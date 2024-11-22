<?php

declare(strict_types=1);

use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$rules = [
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => ['=>' => null],
    ],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_lines_before_namespace' => true,
    'blank_line_between_import_groups' => true,
    'braces_position' => true,
    'cast_spaces' => false,
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
            'trait_import' => 'none',
        ],
    ],
    'class_definition' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'concat_space' => [
        'spacing' => 'one',
    ],
    'constant_case' => [
        'case' => 'lower',
    ],
    'control_structure_braces' => true,
    'control_structure_continuation_position' => true,
    'date_time_immutable' => true,
    'declare_equal_normalize' => true,
    'declare_parentheses' => true,
    'declare_strict_types' => true,
    'elseif' => true,
    'encoding' => true,
    'full_opening_tag' => true,
    'fully_qualified_strict_types' => true,
    'function_declaration' => true,
    'function_to_constant' => true,
    'general_phpdoc_tag_rename' => true,
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'heredoc_to_nowdoc' => true,
    'implode_call' => true,
    'include' => true,
    'increment_style' => ['style' => 'post'],
    'indentation_type' => true,
    'is_null' => true,
    'lambda_not_used_import' => true,
    'linebreak_after_opening_tag' => true,
    'line_ending' => true,
    'lowercase_cast' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_method_casing' => true,
    'magic_constant_casing' => true,
    'method_argument_space' => true,
    'modernize_types_casting' => true,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'no_multi_line',
    ],
    'native_constant_invocation' => true,
    'native_function_casing' => true,
    'new_with_parentheses' => true,
    'no_alias_functions' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'extra',
            'throw',
        ],
    ],
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_break_comment' => true,
    'no_closing_tag' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => [
        'use' => 'echo',
    ],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_multiple_statements_per_line' => true,
    'no_php4_constructor' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => true,
    'no_superfluous_elseif' => true,
    'no_superfluous_phpdoc_tags' => true,
    'no_trailing_comma_in_singleline' => true,
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,
    'no_unneeded_braces' => true,
    'no_unneeded_control_parentheses' => true,
    'no_unneeded_final_method' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unset_on_property' => true,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_sprintf' => true,
    'no_useless_return' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'non_printable_character' => true,
    'normalize_index_brace' => true,
    'not_operator_with_successor_space' => false,
    'nullable_type_declaration_for_default_null_value' => true,
    'object_operator_without_whitespace' => true,
    'ordered_imports' => [
        'case_sensitive' => true,
        'imports_order' => ['class', 'function', 'const'],
        'sort_algorithm' => 'alpha',
    ],
    'ordered_interfaces' => true,
    'ordered_traits' => true,
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_empty_return' => true,
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_tag_type' => true,
    'phpdoc_to_comment' => false,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_var_without_name' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => true,
    'php_unit_dedicate_assert_internal_type' => true,
    'php_unit_expectation' => true,
    'php_unit_mock_short_will_return' => true,
    'php_unit_namespaced' => true,
    'php_unit_size_class' => false,
    'psr_autoloading' => true,
    'self_accessor' => false,
    'short_scalar_cast' => true,
    'simplified_null_return' => true,
    'single_blank_line_at_eof' => true,
    'single_class_element_per_statement' => true,
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    'single_line_comment_style' => [
        'comment_types' => ['hash'],
    ],
    'single_quote' => true,
    'single_space_around_construct' => true,
    'space_after_semicolon' => true,
    'spaces_inside_parentheses' => true,
    'standardize_not_equals' => true,
    'statement_indentation' => true,
    'strict_param' => true,
    'switch_case_semicolon_to_colon' => true,
    'switch_case_space' => true,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline' => true,
    'trim_array_spaces' => true,
    'type_declaration_spaces' => true,
    'unary_operator_spaces' => true,
    'visibility_required' => true,
    'void_return' => true,
    'whitespace_after_comma_in_array' => true,
    'yoda_style' => true,
];

$finder = Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->exclude('bootstrap')
    ->exclude('node_modules')
    ->exclude('storage')
    ->exclude('vendor')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true);
