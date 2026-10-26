<?php

return [
    'required' => ':attribute আবশ্যক।',
    'email'    => ':attribute একটি সঠিক ইমেইল ঠিকানা হতে হবে।',
    'unique'   => ':attribute ইতিমধ্যেই গ্রহণ করা হয়েছে।',
    'min'      => [
        'numeric' => ':attribute অবশ্যই কমপক্ষে :min হতে হবে।',
        'string'  => ':attribute অবশ্যই কমপক্ষে :min অক্ষরের হতে হবে।',
    ],
    'max'      => [
        'numeric' => ':attribute কোনোভাবেই :max এর বেশি হতে পারবে না।',
        'string'  => ':attribute :max অক্ষরের বেশি হতে পারবে না।',
    ],
    'between'  => [
        'numeric' => ':attribute অবশ্যই :min এবং :max এর মধ্যে হতে হবে।',
        'string'  => ':attribute অবশ্যই :min থেকে :max অক্ষরের মধ্যে হতে হবে।',
    ],
    'confirmed' => ':attribute নিশ্চিতকরণ মেলেনি।',
    'numeric'   => ':attribute একটি সংখ্যা হতে হবে।',
    'integer'   => ':attribute অবশ্যই একটি পূর্ণসংখ্যা হতে হবে।',
    'in'        => 'নির্বাচিত :attribute গ্রহণযোগ্য নয়।',
    'exists'    => 'নির্বাচিত :attribute সঠিক নয়।',

    'attributes' => [
        'category_id'              => 'সম্পদের শ্রেণি',
        'requested_amount_bdt'     => 'প্রাক্কলিত মূল্য',
        'reason'                   => 'সম্পদের প্রয়োজনীয়তার বিবরণ',
        'livelihood_justification' => 'জীবিকার যৌক্তিকতা',
        'email'                    => 'ইমেইল ঠিকানা',
        'password'                 => 'পাসওয়ার্ড',
        'name'                     => 'নাম',
        'role'                     => 'ভূমিকা',
        'amount_bdt'               => 'অনুদান পরিমাণ',
        'quoted_amount_bdt'        => 'দরপত্রের মূল্য',
    ],
];
