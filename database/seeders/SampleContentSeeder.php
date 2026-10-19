<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BuyerRequest;
use App\Models\Category;
use App\Models\Country;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Listing;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@farmgate.local'],
            [
                'name' => 'FarmGate Admin',
                'full_name' => 'FarmGate Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'country' => 'Zambia',
                'primary_interest' => 'Agribusiness',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'farmer@farmgate.local'],
            [
                'name' => 'Chileshe Farmer',
                'full_name' => 'Chileshe Farmer',
                'password' => Hash::make('password'),
                'role' => 'registered_user',
                'country' => 'Zambia',
                'primary_interest' => 'Crops',
            ]
        );

        $country = Country::firstOrCreate(['code' => 'ZMB'], ['name' => 'Zambia']);
        $category = Category::firstOrCreate(['slug' => 'crops-and-grains'], ['name' => 'Crops and Grains']);
        $knowledgeCategory = KnowledgeCategory::firstOrCreate(['slug' => 'crop-production'], ['name' => 'Crop Production']);

        $siteSettings = [
            ['key' => 'site_name', 'value' => 'FarmGate Zambia', 'group_name' => 'general'],
            ['key' => 'tagline', 'value' => 'Everything Agriculture, One Place.', 'group_name' => 'general'],
            ['key' => 'contact_email', 'value' => 'hello@farmgate.zm', 'group_name' => 'general'],
            ['key' => 'contact_phone', 'value' => '+260 974 000000', 'group_name' => 'general'],
        ];

        foreach ($siteSettings as $setting) {
            SiteSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $sampleListings = [
            [
                'slug' => 'maize-grain-for-sale',
                'title' => 'Maize grain for sale',
                'description' => 'High-quality maize grain harvested in Central Province, packaged for bulk buyers.',
                'quantity' => 120,
                'unit' => 'bags',
                'price' => 420,
                'image' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'vegetable-seedling-bundle',
                'title' => 'Vegetable seedling bundle',
                'description' => 'Healthy tomato and cabbage seedlings raised in a controlled nursery.',
                'quantity' => 250,
                'unit' => 'seedlings',
                'price' => 150,
                'image' => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'fresh-soybeans-central-province',
                'title' => 'Fresh soybeans from Central Province',
                'description' => 'Clean, dry soybeans suitable for food processing and livestock feed.',
                'quantity' => 75,
                'unit' => 'bags',
                'price' => 680,
                'image' => 'https://images.unsplash.com/photo-1595979862215-37f3d4be3a96?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'sun-dried-groundnuts',
                'title' => 'Sun-dried groundnuts',
                'description' => 'Locally grown groundnuts, carefully dried and ready for wholesale.',
                'quantity' => 40,
                'unit' => 'bags',
                'price' => 1250,
                'image' => 'https://images.unsplash.com/photo-1567892737950-30c4db37cd89?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'fresh-tomatoes-lusaka',
                'title' => 'Fresh tomatoes, Lusaka',
                'description' => 'Firm market-grade tomatoes packed in ventilated crates.',
                'quantity' => 90,
                'unit' => 'crates',
                'price' => 230,
                'image' => 'https://images.unsplash.com/photo-1546094096-0df4bcaaa337?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'organic-free-range-eggs',
                'title' => 'Free-range farm eggs',
                'description' => 'Fresh free-range eggs collected daily from a smallholder poultry farm.',
                'quantity' => 60,
                'unit' => 'trays',
                'price' => 95,
                'image' => 'https://images.unsplash.com/photo-1506976785307-8732e854ad03?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'sunflower-seed-bulk',
                'title' => 'Sunflower seed for oil pressing',
                'description' => 'Clean sunflower seed suitable for local oil processors and feed producers.',
                'quantity' => 55,
                'unit' => 'bags',
                'price' => 540,
                'image' => 'https://images.unsplash.com/photo-1470252649378-9c29740c9fa8?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'fresh-honey-northern-province',
                'title' => 'Pure wildflower honey',
                'description' => 'Raw, unfiltered honey from community-managed apiaries.',
                'quantity' => 120,
                'unit' => 'jars',
                'price' => 85,
                'image' => 'https://images.unsplash.com/photo-1587049352851-8d4e89133924?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'dried-common-beans',
                'title' => 'Dried common beans',
                'description' => 'Sorted, clean common beans with consistent size for retailers and processors.',
                'quantity' => 65,
                'unit' => 'bags',
                'price' => 720,
                'image' => 'https://images.unsplash.com/photo-1515543904379-3d757afe72e4?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'fresh-sweet-potatoes',
                'title' => 'Fresh sweet potatoes',
                'description' => 'Well-cured sweet potatoes harvested to order from a family farm.',
                'quantity' => 100,
                'unit' => 'bags',
                'price' => 310,
                'image' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($sampleListings as $sampleListing) {
            Listing::updateOrCreate(
                ['slug' => $sampleListing['slug']],
                [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'title' => $sampleListing['title'],
                    'description' => $sampleListing['description'],
                    'quantity' => $sampleListing['quantity'],
                    'unit' => $sampleListing['unit'],
                    'price' => $sampleListing['price'],
                    'currency' => 'ZMW',
                    'negotiable' => true,
                    'country_id' => $country->id,
                    'status' => 'approved',
                    'availability' => 'available',
                    'featured_image' => $sampleListing['image'],
                    'is_active' => true,
                ]
            );
        }

        SiteSetting::firstOrCreate(
            ['key' => 'home_slider_listing_ids'],
            [
                'value' => json_encode(
                    Listing::whereIn('slug', array_column(array_slice($sampleListings, 0, 6), 'slug'))
                        ->orderBy('title')
                        ->pluck('id')
                        ->all()
                ),
                'group_name' => 'homepage',
            ],
        );

        BuyerRequest::updateOrCreate(
            ['slug' => 'maize-for-feed-production'],
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Maize for feed production',
                'quantity' => 80,
                'unit' => 'bags',
                'target_price' => 500,
                'quality_requirements' => 'Dry grain with low moisture and free from mould',
                'location' => 'Lusaka',
                'description' => 'Looking for a reliable supplier for feed production in Lusaka.',
                'status' => 'approved',
            ]
        );

        KnowledgeArticle::updateOrCreate(
            ['slug' => 'smart-irrigation-for-smallholder-farms'],
            [
                'category_id' => $knowledgeCategory->id,
                'title' => 'Smart irrigation for smallholder farms',
                'summary' => 'Practical ideas for improving water efficiency and crop resilience.',
                'body' => '<p>Efficient irrigation can help farmers reduce losses while improving yields.</p><p>Start by scheduling irrigation based on crop stage and soil moisture readings.</p>',
                'author_id' => $admin->id,
                'reviewer_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
                'reading_time' => 5,
                'seo_title' => 'Smart irrigation for smallholder farms',
                'seo_description' => 'Learn practical irrigation techniques for resilient smallholder farming in Zambia.',
                'tags' => 'irrigation, farming, water management',
                'is_featured' => true,
            ]
        );

        $guides = [
            [
                'soil-health-maize', 'Build healthier soils for better maize harvests',
                'Simple rotation, composting, and soil-cover practices that improve soil structure and crop resilience.',
                'soil, maize, conservation', 'crop-production', 'Crop Production',
                'Rotate maize with legumes where suitable, keep soil covered between crops, and return healthy crop residues to the field. Before changing fertilizer use, compare soil tests or seek local extension advice so nutrients match the field and crop needs.',
            ],
            [
                'smallholder-poultry-basics', 'Smallholder poultry: a practical starter guide',
                'Plan housing, clean water, feed, vaccination, and daily checks before growing your flock.',
                'poultry, livestock, biosecurity', 'livestock', 'Livestock',
                'Prepare a dry, well-ventilated shelter with enough space, clean water, and feed before bringing in birds. Keep new or sick birds separate, clean drinkers and feeders, and ask a local animal-health professional about a vaccination plan for your area.',
            ],
            [
                'post-harvest-grain-storage', 'Keep grain safe after harvest',
                'Drying, sorting, and storage steps that help protect grain from moisture, insects, and avoidable losses.',
                'storage, post-harvest, grain', 'post-harvest', 'Post-harvest handling',
                'Harvest mature grain, remove damaged material, and dry it to the safe level recommended by local extension officers before storage. Use clean, dry bags or approved containers, keep them off floors and away from walls, and inspect stored grain regularly for moisture or pests.',
            ],
            [
                'dry-season-vegetable-gardening', 'Grow vegetables through the dry season',
                'Choose suitable crops, conserve water, and plan planting cycles for a productive dry-season garden.',
                'vegetables, irrigation, dry season', 'crop-production', 'Crop Production',
                'Choose varieties suited to local conditions and plan planting dates around reliable water access. Water near the roots in the cooler parts of the day, use a clean mulch to slow evaporation, and stagger planting so produce is ready in manageable batches.',
            ],
            [
                'farm-record-keeping', 'Farm records that support better decisions',
                'A simple system for tracking inputs, labour, harvests, sales, and cash flow on a small farm.',
                'business, records, planning', 'farm-business', 'Farm business',
                'Record each purchase and sale with its date, quantity, unit, and amount; also note labour, transport, and other production costs. At the end of each season, compare the records with harvest and sales so you can identify profitable crops and plan inputs with more confidence.',
            ],
            [
                'safe-pesticide-use', 'Use crop protection products safely',
                'Read labels, wear protective equipment, store products securely, and observe application guidance.',
                'crop protection, safety, inputs', 'crop-protection', 'Crop protection',
                'Use only products approved for the crop and pest, and follow the product label and local extension advice. Wear the protective equipment specified on the label, keep people and animals away during application, observe re-entry and pre-harvest intervals, and store products in their original containers away from food and children.',
            ],
            [
                'composting-farm-waste', 'Turn farm waste into useful compost',
                'Balance green and dry materials, manage moisture, and use mature compost to support healthy crops.',
                'compost, soil health, recycling', 'crop-production', 'Crop Production',
                'Combine a mix of fresh green material and dry plant matter, chop bulky pieces, and keep the heap moist but not waterlogged. Turn it when the centre cools or smells unpleasant; finished compost is dark and crumbly. Avoid adding plastics, treated wood, or waste contaminated with chemicals.',
            ],
            [
                'market-ready-produce', 'Prepare produce for a stronger market',
                'Practical grading, cleaning, packing, and buyer communication tips to improve produce presentation.',
                'marketing, produce, buyers', 'farm-business', 'Farm business',
                'Ask buyers about preferred varieties, grades, quantities, delivery days, and packaging before harvest. Sort out damaged produce, use clean suitable packaging, and agree on price and delivery details in writing where possible to reduce misunderstandings.',
            ],
            [
                'climate-smart-farm-planning', 'Plan a more climate-resilient farm season',
                'Use local forecasts, diversify crops, conserve soil moisture, and prepare for changing conditions.',
                'climate, planning, resilience', 'climate-resilience', 'Climate resilience',
                'Use local seasonal forecasts and extension updates as planning inputs, not guarantees. Diversify crops where practical, protect soil with cover and mulch, maintain drainage where heavy rain is a risk, and identify backup water and seed options before the season becomes difficult.',
            ],
        ];

        foreach ($guides as [$slug, $title, $summary, $tags, $categorySlug, $categoryName, $body]) {
            $guideCategory = KnowledgeCategory::firstOrCreate(
                ['slug' => $categorySlug],
                ['name' => $categoryName],
            );

            KnowledgeArticle::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $guideCategory->id,
                    'title' => $title,
                    'summary' => $summary,
                    'body' => '<p>'.$summary.'</p><p>'.$body.'</p><p>Start with a manageable step, keep notes on what works in your location, and consult local extension services for advice tailored to your crop and conditions. Share practical results with the FarmGate community so other farmers can learn from your experience.</p>',
                    'author_id' => $admin->id,
                    'reviewer_id' => $admin->id,
                    'status' => 'published',
                    'published_at' => now(),
                    'reading_time' => 4,
                    'seo_title' => $title,
                    'seo_description' => $summary,
                    'tags' => $tags,
                    'is_featured' => false,
                ]
            );
        }

        Business::updateOrCreate(
            ['name' => 'Green Valley Agro'],
            [
                'user_id' => $admin->id,
                'business_type' => 'Agricultural Inputs',
                'description' => 'Trusted supplier of fertilizers, crop protection and farm inputs.',
                'phone' => '+260 968 401010',
                'email' => 'sales@greenvalley.co.zm',
                'country_id' => $country->id,
                'province_id' => null,
                'district_id' => null,
                'display_address' => 'Lusaka',
                'status' => 'approved',
            ]
        );
    }
}
