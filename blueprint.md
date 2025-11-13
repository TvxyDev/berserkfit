# Blueprint: BerserkFit Application

This document outlines the design, features, and development plan of the BerserkFit application, a modern web app for fitness tracking. The project includes a public-facing landing page, user authentication (login/registration), and a private user dashboard.

## Project State

### Landing Page (`index.html`)

*   **Purpose:** To promote the BerserkFit app and encourage user sign-ups.
*   **Layout:** A single-page, responsive layout with multiple sections (Início, Funcionalidades, Planos, Sobre, Depoimentos, Contato).
*   **Styling:** Dark, modern aesthetic with a color palette of dark blues, purples, and yellow accents. Typography uses "Poppins" for headers and "Inter" for body text. The page features a sticky header, smooth scrolling, and scroll-triggered fade-in animations.
*   **Key Sections:**
    *   **Hero:** Impactful title and call-to-action.
    *   **Features:** Icon-based highlights of app capabilities.
    *   **Pricing:** Tiered subscription plans.
    *   **Testimonials:** Social proof from fictional users.
    *   **Contact:** A form and social media links.

### User Authentication

*   **Login (`login.html`):** A dedicated page for existing users to sign in. The design is consistent with the landing page's dark theme.
*   **Registration (`registro.html`):** A page for new users to create an account.

### Dashboard (`dashboard.html`)

*   **Purpose:** The main interface for logged-in users to track their daily and weekly fitness progress.
*   **Layout:** A responsive, single-page dashboard with a fixed bottom navigation bar for mobile-first interaction.
*   **Styling (`dashboard.css`):
    *   **Theme:** Dark theme (`#121212` background) with vibrant blue accents (`#007BFF`) for interactive elements and progress indicators.
    *   **Typography:** "Inter" font for a clean, modern look.
    *   **Animations:** Subtle fade-in effects for all sections to enhance user experience.
*   **Components:**
    *   **Header:** Displays a personalized greeting, the user's avatar, and the current date.
    *   **Daily Summary:** A grid of four cards, each showing a key metric (Water, Calories, Training Minutes, Sleep) with a visual progress bar.
    *   **Daily Goals:** An interactive checklist for users to mark their daily tasks as complete.
    *   **Progress Chart:** A placeholder for a future weekly progress graph (e.g., using Chart.js).
    *   **Tip of the Day:** A motivational quote or fitness tip presented in a distinct card.
    *   **Bottom Navigation Bar:** A fixed navbar at the bottom of the screen, providing easy access to the main sections of the app (Home, Treinos, Progresso, IA, Perfil). The active link has a highlighted background, inspired by the user-provided model.

## Current Development Plan

### Task: Create User Dashboard

1.  **Create `dashboard.html`:**
    *   **Action:** Developed the HTML structure for the dashboard.
    *   **Details:** Included a header for greetings, sections for daily summary, daily goals, a progress chart placeholder, and a tip of the day. Implemented the structure for the fixed bottom navigation bar.
    *   **Status:** ✅ **Completed**
2.  **Create `dashboard.css`:**
    *   **Action:** Designed the stylesheet for the dashboard page.
    *   **Details:** Implemented the dark theme, blue accents, card-based layout, progress bars, and the custom-styled fixed bottom navigation bar. Added responsive styles for smaller screens and fade-in animations for all sections.
    *   **Status:** ✅ **Completed**
3.  **Update Blueprint (`blueprint.md`):**
    *   **Action:** Documented the creation of the dashboard page, its features, and its styling.
    *   **Status:** ✅ **Completed**