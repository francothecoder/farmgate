<?php

namespace Tests\Feature;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnowledgeCommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_sign_in_before_posting_to_a_knowledge_discussion(): void
    {
        $article = $this->publishedArticle();

        $this->post(route('knowledge.comments.store', $article), [
            'body' => 'A question for the community.',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('knowledge_comments', 0);
    }

    public function test_member_posts_a_comment_for_moderation_and_approved_discussion_is_visible(): void
    {
        $member = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $article = $this->publishedArticle();

        $this->actingAs($member)->post(route('knowledge.comments.store', $article), [
            'body' => 'How often should this be done?',
        ])->assertRedirect()
            ->assertSessionHas('success', 'Your contribution is awaiting moderator approval.');

        $comment = KnowledgeComment::firstOrFail();
        $this->assertSame('pending', $comment->status);
        $this->get(route('knowledge.show', $article))->assertDontSee('How often should this be done?');

        $this->actingAs($admin)->patch(route('admin.knowledge-comments.update', $comment), [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('knowledge_comments', [
            'id' => $comment->id,
            'status' => 'approved',
            'reviewed_by' => $admin->id,
        ]);
        $this->get(route('knowledge.show', $article))
            ->assertOk()
            ->assertSee('How often should this be done?');
    }

    public function test_member_can_reply_to_an_approved_discussion_and_reply_waits_for_review(): void
    {
        $member = User::factory()->create();
        $article = $this->publishedArticle();
        $parent = $article->comments()->create([
            'user_id' => User::factory()->create()->id,
            'body' => 'What works for sandy soil?',
            'status' => 'approved',
        ]);

        $this->actingAs($member)->post(route('knowledge.comments.store', $article), [
            'parent_id' => $parent->id,
            'body' => 'Adding compost helped on our farm.',
        ])->assertRedirect();

        $this->assertDatabaseHas('knowledge_comments', [
            'knowledge_article_id' => $article->id,
            'parent_id' => $parent->id,
            'user_id' => $member->id,
            'status' => 'pending',
            'body' => 'Adding compost helped on our farm.',
        ]);
        $this->get(route('knowledge.show', $article))->assertDontSee('Adding compost helped on our farm.');
    }

    public function test_member_cannot_reply_to_a_comment_on_another_article(): void
    {
        $member = User::factory()->create();
        $article = $this->publishedArticle();
        $otherArticle = $this->publishedArticle('another-guide');
        $parent = $otherArticle->comments()->create([
            'user_id' => User::factory()->create()->id,
            'body' => 'A separate discussion.',
            'status' => 'approved',
        ]);

        $this->actingAs($member)->from(route('knowledge.show', $article))
            ->post(route('knowledge.comments.store', $article), [
                'parent_id' => $parent->id,
                'body' => 'Trying to cross-post.',
            ])->assertSessionHasErrors('parent_id');

        $this->assertDatabaseMissing('knowledge_comments', [
            'user_id' => $member->id,
            'body' => 'Trying to cross-post.',
        ]);
    }

    public function test_non_admin_cannot_moderate_knowledge_discussions(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.knowledge-comments.index'))->assertForbidden();
    }

    public function test_approved_discussion_text_is_escaped_when_rendered(): void
    {
        $article = $this->publishedArticle();
        $article->comments()->create([
            'user_id' => User::factory()->create()->id,
            'body' => '<script>alert(1)</script>',
            'status' => 'approved',
        ]);

        $this->get(route('knowledge.show', $article))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    private function publishedArticle(string $slug = 'soil-health'): KnowledgeArticle
    {
        $category = KnowledgeCategory::firstOrCreate(
            ['slug' => 'crop-production'],
            ['name' => 'Crop Production'],
        );

        return KnowledgeArticle::create([
            'category_id' => $category->id,
            'title' => 'Soil health',
            'slug' => $slug,
            'body' => 'A practical guide to healthy soil.',
            'author_id' => User::factory()->create()->id,
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
