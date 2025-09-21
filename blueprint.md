# Blueprint: BerserkFit Landing Page

## Overview

This document outlines the design and features of the BerserkFit landing page, a single-page website designed to promote a fitness application. The page is built with modern HTML, CSS, and JavaScript, following the guidelines provided.

## Project State

### Design & Style

*   **Color Palette:**
    *   `--cor-fundo`: #180F3C (Main background)
    *   `--cor-primaria`: #00010D (Cards, footer, forms)
    *   `--cor-secundaria`: #020126 (Borders, hover effects)
    *   `--cor-destaque`: #F3F4F5 (Text, non-featured borders)
    *   `--cor-texto`: #F3F4F5 (Main text color, header background)
    *   `--cor-amarela`: #FFD700 (Pricing highlight, hero button, testimonial details)
*   **Typography:**
    *   `Poppins`: For titles and headings.
    *   `Inter`: For body text.
*   **Layout:**
    *   Fully responsive single-page layout with a sticky header.
    *   Sections: Início, Funcionalidades, Planos, Sobre, Depoimentos, Contato.
    *   CSS Grid is used for responsive layouts in the features, pricing, about, testimonials, and contact sections.
*   **Visuals:**
    *   Main logo (`logotipo1.png`) in the header.
    *   Hero section (`#inicio`) features a full-bleed background image (`background1.png`) with a dark overlay.
    *   Warrior portraits in the "Our Legion" section.
    *   Custom SVG icons for features, pricing checkmarks, and social media.
    *   Subtle noise texture on the main background.

### Features Implemented

*   **Header:** 
    *   Sticky header with a light background that becomes translucent on scroll.
    *   The `header-scrolled` class applies a semi-transparent background and a `backdrop-filter` for a "frosted glass" effect.
*   **Hero Section:** 
    *   Large, impactful hero with a custom background, an epic-sized title (`h1`), a descriptive subtitle (`.subtitulo-heroi`), and a prominent call-to-action button (`.botao-heroi`).
*   **Features Section:** Three-card layout highlighting key app features.
*   **Pricing Section:** 
    *   A responsive three-card pricing table.
    *   The "Gladiator" plan is featured with a yellow border and a matching call-to-action button.
*   **About Us Section:** 
    *   Split into "Our Mission" and "Our Legion".
    *   "Our Legion" section introduces the team with warrior portraits and titles.
*   **Testimonials Section:**
    *   A new section titled "O Que Dizem Nossos Guerreiros" to build social proof.
    *   Features a three-card grid (`grade-depoimentos`) with user testimonials.
    *   Each card (`cartao-depoimento`) includes a user avatar, a quote, the user's name, and their plan tier.
*   **Contact Section:** A two-column layout with a contact form and social media links.
*   **Footer:** Contains links and copyright information.
*   **Smooth Scrolling:** Implemented for anchor links.
*   **Scroll-triggered Animations:** Elements in each section fade in smoothly as the user scrolls down the page.

## Development Plan

### Task: Refine Hero Section and Add Testimonials (Completed)

1.  **Update Hero Section (`#inicio`):**
    *   Modified the main title `h1` to be larger and more impactful.
    *   Replaced the old subtitle with a new, more descriptive one (`.subtitulo-heroi`).
    *   Re-introduced a primary call-to-action button (`.botao-heroi`) with the text "Começar Agora" and styled it with a vibrant yellow background to draw attention.
2.  **Add Testimonials Section (`#depoimentos`):
    *   Added a new "Depoimentos" link to the main navigation in `index.html`.
    *   Created the new section in `index.html` with a title and a grid for the testimonials.
    *   Used placeholder images from `i.pravatar.cc` for user avatars.
    *   Added styles to `estilo.css` for `.grade-depoimentos` and `.cartao-depoimento` to match the site's aesthetic.
    *   Ensured the new section is responsive and integrates with the existing fade-in animation.
3.  **Update Blueprint (`blueprint.md`):** Documented the new section, the hero section updates, and all associated style changes.