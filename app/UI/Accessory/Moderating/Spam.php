<?php

namespace App\UI\Accessory\Moderating;

final class Spam
{
    public const SPAM_WORDS = [
        'spam',
        'lorem',
        'ipsum',
        'dolore',
        'magna',
        'aliqua',
        '123456791',
        '1111111111',
        '0000000000',
        '123',
        '9999999999',
        '2222222222',
        '8888888888',
        '7777777777',
        '6666666666',
        '5555555555',
        '4444444444',
        '3333333333',
        'blabla',
        'fuck',
        'suck',
        'asshole',
        '<',
        '>',
        '{',
        '}',
        '$',
        'hacker',
        'hack',
        'html',
        'script',
        'alert()',
        'inject',
        '100%',
        '#1',
        '$$$',
        '100% free',
        '100% Satisfied',
        '4U',
        '50% off',
        'Accept credit cards',
        'Acceptance',
        'Access',
        'Accordingly',
        'Act Now',
        'Action',
        'Ad',
        'Additional income',
        'Additional Income',
        'Addresses on CD',
        'Affordable',
        'All natural',
        'All new',
        'Amazed',
        'Amazing',
        'Amazing stuff',
        'Apply now',
        'Apply Online',
        'As seen on',
        'Auto email removal',
        'Avoid',
        'Avoid bankruptcy',
        'Bargain',
        'Be amazed',
        'Be your own boss',
        'Being a member',
        'Beneficiary',
        'Best price',
        'Beverage',
        'Big bucks',
        'Bill 1618',
        'Billing',
        'Billing address',
        'Billion',
        'Billion dollars',
        'Bonus',
        'Boss',
        'Brand new pager',
        'Bulk email',
        'Buy',
        'Buy direct',
        'Buying judgments',
        'Cable converter',
        'Call',
        'Call free',
        'Call now',
        'Calling creditors',
        'Can’t live without',
        'Cancel',
        'Cancel at any time',
        'Cannot be combined with any other offer',
        'Cards accepted',
        'Cash',
        'Cash bonus',
        'Cashcashcash',
        'Casino',
        'Celebrity',
        'Cell phone cancer scam',
        'Cents on the dollar',
        'Certified',
        'Chance',
        'Cheap',
        'Check',
        'Check or money order',
        'Claims',
        'Claims not to be selling anything',
        'Claims to be in accordance with some spam law',
        'Claims to be legal',
        'Clearance',
        'Click',
        'Click below',
        'Click here',
        'Click to remove',
        'Collect',
        'Collect child support',
        'Compare',
        'Compare rates',
        'Compete for your business',
        'Confidentially on all orders',
        'Congratulations',
        'Consolidate debt and credit',
        'Consolidate your debt',
        'Copy accurately',
        'Copy DVDs',
        'Costs',
        'Credit',
        'Credit bureaus',
        'Credit card offers',
        'Cures',
        'Cures baldness',
        'Deal',
        'Dear [email/friend/somebody]',
        'Debt',
        'Diagnostics',
        'Dig up dirt on friends',
        'Direct email',
        'Direct marketing',
        'Discount',
        'Do it today',
        'Don’t delete',
        'Don’t hesitate',
        'Dormant',
        'Double your',
        'Double your cash',
        'Double your income',
        'Drastically reduced',
        'Earn',
        'Earn $',
        'Earn extra cash',
        'Earn per week',
        'Easy terms',
        'Eliminate bad credit',
        'Eliminate debt',
        'Email harvest',
        'Email marketing',
        'Exclusive deal',
        'Expect to earn',
        'Expire',
        'Explode your business',
        'Extra',
        'Extra cash',
        'Extra income',
        'F r e e',
        'Fantastic',
        'Fantastic deal',
        'Fast cash',
        'Fast Viagra delivery',
        'Financial freedom',
        'Financially independent',
        'For free',
        'For instant access',
        'For just $ (some amount)',
        'For just $xxx',
        'For Only',
        'For you',
        'Form',
        'Free',
        'Free access',
        'Free cell phone',
        'Free consultation',
        'Free DVD',
        'Free gift',
        'Free grant money',
        'Free hosting',
        'Free info',
        'Free installation',
        'Free Instant',
        'Free investment',
        'Free leads',
        'Free membership',
        'Free money',
        'Free offer',
        'Free preview',
        'Free priority mail',
        'Free quote',
        'Free sample',
        'Free trial',
        'Free website',
        'Freedom',
        'Friend',
        'Full refund',
        'Get',
        'Get it now',
        'Get out of debt',
        'Get paid',
        'Get started now',
        'Gift certificate',
        'Give it away',
        'Giving away',
        'Great',
        'Great offer',
        'Guarantee',
        'Guaranteed',
        'Have you been turned down?',
        'Hello',
        'Here',
        'Hidden',
        'Hidden assets',
        'Hidden charges',
        'Home',
        'Home based',
        'Home employment',
        'Home based business',
        'Human growth hormone',
        'If only it were that easy',
        'Important information regarding',
        'In accordance with laws',
        'Income',
        'Income from home',
        'Increase sales',
        'Increase traffic',
        'Increase your sales',
        'Incredible deal',
        'Info you requested',
        'Information you requested',
        'Insurance',
        'Internet market',
        'Internet marketing',
        'Investment',
        'Investment decision',
        'It’s effective',
        'Join millions',
        'Join millions of Americans',
        'Junk',
        'Laser printer',
        'Leave',
        'Legal',
        'Life',
        'Life Insurance',
        'Lifetime',
        'Limited',
        'limited time',
        'Limited time offer',
        'Limited time only',
        'Loan',
        'Long distance phone offer',
        'Lose',
        'Lose weight',
        'Lose weight spam',
        'Lower interest rates',
        'Lower monthly payment',
        'Lower your mortgage rate',
        'Lowest insurance rates',
        'Lowest Price',
        'Luxury',
        'Luxury car',
        'Mail in order form',
        'Maintained',
        'Make $',
        'Make money',
        'Marketing',
        'Marketing solutions',
        'Mass email',
        'Medicine',
        'Medium',
        'Meet singles',
        'Member',
        'Member stuff',
        'Message contains',
        'Message contains disclaimer',
        'Million',
        'Million dollars',
        'Miracle',
        'MLM',
        'Money',
        'Money back',
        'Money making',
        'Month trial offer',
        'More Internet Traffic',
        'Mortgage',
        'Mortgage rates',
        'Multi-level marketing',
        'Name brand',
        'Never',
        'New customers only',
        'New domain extensions',
        'Nigerian',
        'No age restrictions',
        'No catch',
        'No claim forms',
        'No cost',
        'No credit check',
        'No disappointment',
        'No experience',
        'No fees',
        'No gimmick',
        'No hidden',
        'No hidden Costs',
        'No interests',
        'No inventory',
        'No investment',
        'No medical exams',
        'No middleman',
        'No obligation',
        'No purchase necessary',
        'No questions asked',
        'No selling',
        'No strings attached',
        'No-obligation',
        'Not intended',
        'Not junk',
        'Not spam',
        'Now only',
        'Obligation',
        'Offshore',
        'Offer',
        'Offer expires',
        'Orders shipped by',
        'Once in lifetime',
        'One hundred percent free',
        'One hundred percent guaranteed',
        'One time',
        'One time mailing',
        'Online biz opportunity',
        'Online degree',
        'Online marketing',
        'Online pharmacy',
        'Only $',
        'Open',
        'Opportunity',
        'Opt in',
        'Order',
        'Order now',
        'Order shipped by',
        'Order status',
        'Order today',
        'Outstanding values',
        'Passwords',
        'Pennies a day',
        'Per day',
        'Per week',
        'Performance',
        'Phone',
        'Please read',
        'Potential earnings',
        'Pre-approved',
        'Presently',
        'Print form signature',
        'Print out and fax',
        'Priority mail',
        'Prize',
        'Problem',
        'Produced and sent out',
        'Profits',
        'Promise',
        'Promise you',
        'Purchase',
        'Pure Profits',
        'Quote',
        'Rates',
        'Real thing',
        'Refinance',
        'Refinance home',
        'Refund',
        'Removal',
        'Removal instructions',
        'Remove',
        'Removes wrinkles',
        'Request',
        'Requires initial investment',
        'Reserves the right',
        'Reverses',
        'Reverses aging',
        'Risk free',
        'Rolex',
        'Round the world',
        'S 1618',
        'Safeguard notice',
        'Sale',
        'Sample',
        'Satisfaction',
        'Satisfaction guaranteed',
        'Save $',
        'Save big money',
        'Save up to',
        'Score',
        'Score with babes',
        'Search engine listings',
        'Search engines',
        'Section 301',
        'See for yourself',
        'Sent in compliance',
        'Serious',
        'Serious cash',
        'Serious only',
        'Shopper',
        'Shopping spree',
        'Sign up free today',
        'shopper',
        'Social security number',
        'Solution',
        'Spam',
        'Special promotion',
        'Stainless steel',
        'Stock alert',
        'Stock disclaimer statement',
        'Stock pick',
        'Stop',
        'Stop snoring',
        'Strong buy',
        'Stuff on sale',
        'Subject to cash',
        'Subject to credit',
        'Subscribe',
        'Success',
        'Supplies',
        'Supplies are limited',
        'Take action',
        'Take action now',
        'Talks about hidden charges',
        'Talks about prizes',
        'Teen',
        'Tells you it’s an ad',
        'Terms',
        'Terms and conditions',
        'The best rates',
        'The following form',
        'They keep your money — no refund!',
        'They’re just giving it away',
        'This isn’t a scam',
        'This isn’t junk',
        'This isn’t spam',
        'This won’t last',
        'Thousands',
        'Time limited',
        'Trial',
        'Undisclosed recipient',
        'University diplomas',
        'Unlimited',
        'Unsecured credit',
        'Unsecured debt',
        'Unsolicited',
        'Unsubscribe',
        'Urgent',
        'US dollars',
        'Vacation',
        'Vacation offers',
        'Valium',
        'Vicodin',
        'Visit our website',
        'Wants credit card',
        'Warranty',
        'We hate spam',
        'We honor all',
        'Web traffic',
        'Weekend getaway',
        'Weight',
        'Weight loss',
        'What are you waiting for?',
        'What’s keeping you?',
        'While supplies last',
        'While you sleep',
        'Who really wins?',
        'Why pay more?',
        'Wife',
        'Will not believe your eyes',
        'Win',
        'Winner',
        'Winning',
        'Won',
        'Work from home',
        'Xanax',
        'You are a winner!',
        'You have been selected',
        'Your income',
        'Homebased business',
        'Work at home',
        'For just public $XXX',
        'Loans',
        'Lowest price',
        'Pure profit',
        'They keep your money -- no refund!',
        'Accept Credit Cards',
        'Multi level marketing',
        'Notspam',
        'Sales',
        "This isn't junk",
        "This isn't spam",
        'Off shore',
        'Prizes',
        'unlimited',
        'You’re a Winner!',
        'Act Now!',
        "Can't live without",
        "Don't delete",
        "Don't hesitate",
    ];

    public static function isSpam(string $text)
    {
    }

    public static function cleanText()
    {
    }
}
