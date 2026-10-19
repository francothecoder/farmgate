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

        Listing::updateOrCreate(
            ['slug' => 'maize-grain-for-sale'],
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Maize grain for sale',
                'description' => 'High-quality maize grain harvested in Central Province, packaged for bulk buyers.',
                'quantity' => 120,
                'unit' => 'bags',
                'price' => 420,
                'currency' => 'ZMW',
                'negotiable' => true,
                'country_id' => $country->id,
                'status' => 'approved',
                'availability' => 'available',
                'featured_image' => 'https://images.unsplash.com/photo-1501004318641-b39e6451bec6?auto=format&fit=crop&w=1200&q=80',
                'is_active' => true,
            ]
        );

        Listing::updateOrCreate(
            ['slug' => 'vegetable-seedling-bundle'],
            [
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Vegetable seedling bundle',
                'description' => 'Healthy tomato and cabbage seedlings raised in a controlled nursery.',
                'quantity' => 250,
                'unit' => 'seedlings',
                'price' => 150,
                'currency' => 'ZMW',
                'negotiable' => false,
                'country_id' => $country->id,
                'status' => 'approved',
                'availability' => 'available',
                'featured_image' => 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=1200&q=80',
                'is_active' => true,
            ]
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
