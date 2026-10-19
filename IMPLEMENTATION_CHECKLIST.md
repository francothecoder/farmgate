# FarmGate Zambia V1.1 — Implementation Checklist

## Phase 1: Foundation
- [x] Confirm PHP, Composer, Node, and database tooling availability
- [x] Scaffold Laravel application using the current stable framework
- [x] Install authentication and Livewire support
- [x] Set up the project base architecture and route structure
- [x] Create user, location, category, and content domain models
- [x] Create core database migrations for marketplace, buyer requests, knowledge, and business entities
- [x] Build initial seeders for Zambia location data and sample content

## Phase 2: Public Website
- [x] Create premium landing page and branded navigation
- [x] Implement dynamic category and featured listing sections
- [x] Add marketplace, knowledge hub, and business directory public routes
- [x] Create public product, knowledge, and business detail pages

## Phase 3: Marketplace
- [x] Add listing model, filters, and detail views
- [x] Add enquiry submission flow for marketplace sellers
- [x] Support public marketplace browsing with search and price filtering

## Phase 4: Buyer Requests
- [x] Add buyer request browsing and detail views
- [x] Support seller offer submission to buyer requests

## Phase 5: Knowledge Hub
- [x] Add article listing and article detail pages
- [x] Support category-led article browsing and featured content

## Phase 6: Business Directory
- [x] Add business directory listing and profile views

## Phase 7: Dashboards
- [x] Create authenticated dashboard with recent activity summaries
- [x] Create admin dashboard with operational KPIs
- [x] Protect admin routes behind role checks

## Phase 8: QA and Documentation
- [x] Add automated tests for registration, homepage rendering, and admin authorization
- [x] Document the installation and local development flow in the README
- [x] Verify the app migrates and seeds successfully in the local environment

## V1.1: Operations and Content
- [x] Add seller listing creation with bounded, validated multi-image uploads
- [x] Add admin queues for listing, buyer request, business, and report moderation
- [x] Record moderation decisions, actor, and reason in an audit table
- [x] Add article authoring, Markdown content, SEO fields, editorial statuses, and cover-image uploads
- [x] Add editable site contact and branding settings
- [x] Add an accessible homepage image carousel populated from approved/published content
- [x] Hide unapproved marketplace, buyer-request, business, and article records from public detail routes
- [x] Add feature tests for uploads, authorization, moderation, CMS publishing, and public visibility
