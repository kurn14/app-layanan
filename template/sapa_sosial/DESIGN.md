---
name: Sapa Sosial
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#3e4947'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#6e7977'
  outline-variant: '#bdc9c6'
  surface-tint: '#006a63'
  primary: '#005c55'
  on-primary: '#ffffff'
  primary-container: '#0f766e'
  on-primary-container: '#a3faef'
  inverse-primary: '#80d5cb'
  secondary: '#495f82'
  on-secondary: '#ffffff'
  secondary-container: '#bfd5fe'
  on-secondary-container: '#465c7f'
  tertiary: '#734700'
  on-tertiary: '#ffffff'
  tertiary-container: '#945d00'
  on-tertiary-container: '#ffe6cc'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9cf2e8'
  primary-fixed-dim: '#80d5cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#00504a'
  secondary-fixed: '#d5e3ff'
  secondary-fixed-dim: '#b1c7f0'
  on-secondary-fixed: '#001c3b'
  on-secondary-fixed-variant: '#314769'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
  canvas-bg: '#F8FAFC'
  surface-card: '#FFFFFF'
  status-success: '#10B981'
  status-warning: '#F59E0B'
  status-error: '#EF4444'
  status-info: '#0284C7'
typography:
  display-hero:
    fontFamily: Plus Jakarta Sans
    fontSize: 40px
    fontWeight: '800'
    lineHeight: 48px
    letterSpacing: -0.02em
  display-hero-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '800'
    lineHeight: 38px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '700'
    lineHeight: 28px
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg-accessible:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-default:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-medium:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  code-tracking:
    fontFamily: JetBrains Mono
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.04em
  caption:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 18px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-tablet: 2rem
  margin-desktop: 3rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style

### Brand Personality & Core Values
The design system establishes a welcoming, dependable, and highly accessible public service interface for the residents of Kabupaten Blitar. Civic digital portals frequently alienate citizens through bureaucratic jargon, visual clutter, and cramped targets. This system bridges administrative authority and communal warmth, embodying four foundational values:
- **Ramah & Terbuka (Approachable & Human):** Eliminating institutional anxiety through clear language, comfortable spacing, and gentle visual feedback.
- **Transparan & Akuntabel (Transparent & Trustworthy):** Reassuring users at every step with real-time status tracking, explicit requirements, and uncompromised privacy protection (masking sensitive PII such as NIK and socioeconomic scores on public lookups).
- **Inklusif & Mudah Dipahami (Universally Accessible):** Engineered specifically for multi-generational usability, accommodating elderly users and mobile-first citizens through comfortable typography and forgiving interaction targets.
- **Pasti & Mengayomi (Protective & Reliable):** Providing clear next steps, time estimates, and direct municipal support without ambiguity.

### Design Movement & Aesthetic Form
The system adopts an empathetic **Modern Civic Card-Based** aesthetic. Rather than dense tabular interfaces common in governmental systems, content is modularized into discrete, high-clarity surfaces grounded on an airy neutral canvas. The visual style avoids heavy skeuomorphism and stark brutalism in favor of soft ambient elevation, rounded contours (12–16px), and generous structural white space that respects cognitive load.

## Colors

### Hierarchy & Functional Roles
- **Primary Deep Teal (`#0F766E`):** Represents civic duty, renewal, and care. Used as the principal interactive anchor across primary action buttons, active timeline milestones, primary tab selections, and focused input indicators.
- **Dark Navy (`#0B2545`):** Denotes municipal governance and structural permanence. Applied to primary headings, navigational identity bars, and deep footer containers to ensure commanding contrast and reading legibility.
- **Warm Amber (`#F59E0B`):** Acts as a high-visibility accent for priority alerts, critical triage items ("Layanan Prioritas Darurat"), and actionable warnings that require citizen review.
- **Canvas Neutral (`#F8FAFC`) & Slate (`#64748B`):** The cool neutral background creates an open, distraction-free plane that elevates pure white surface cards, while slate serves as the secondary and muted text hierarchy.

### Accessibility & Contrast Principles
The system operates exclusively in a calibrated light mode to guarantee broad readability in variable outdoor lighting conditions common in regional field contexts. All text-to-background combinations meet or exceed WCAG 2.1 AA standards:
- Dark Navy (`#0B2545`) against Canvas (`#F8FAFC`) produces an exceptional contrast ratio of 13.5:1.
- Deep Teal (`#0F766E`) text on white surfaces yields a contrast ratio of 5.5:1.
- Status notification backgrounds must always use diluted 10% opacity tints paired with high-contrast text shades to preserve rapid comprehension without optical fatigue.

## Typography

### Structural Decisions
- **Unified Font Family:** Plus Jakarta Sans is deployed across headlines, body, and UI labels. Its geometric structure combined with humanist apertures guarantees warmth, clarity, and rapid scanning for readers of varying literacy levels.
- **Strict Size Floor:** Standard body text is strictly locked to a minimum of 16px. A dedicated `body-lg-accessible` (18px) tier is provided for citizen reading flows, procedural instructions, and assisted accessibility modes.
- **Identification & Tracking Codes:** Ticket references and verification codes (`DTSEN-202610-00012`, `PBI-202610-00007`) leverage JetBrains Mono to clearly separate bureaucratic identifiers from conversational prose, mitigating character confusion (e.g., distinguishing `0` and `O`, `1` and `I`).
- **Language Nuance:** All labels, placeholders, and error messages use natural, courteous Bahasa Indonesia (e.g., using "Silakan unggah dokumen persyaratan" instead of mechanical system terms like "Input file").

## Layout & Spacing

### Layout Architecture
- **Mobile-First Foundation (375px baseline):** Layouts stack into a clean single column. Action controls dock into a sticky bottom navigation or floating CTA container with safe-area padding to allow effortless single-thumb submission.
- **Asymmetric Desktop Grid (12 Columns, Max Width 1240px):** Form wizards, detail pages, and complaint flows employ an 8+4 column split. The left 8 columns contain the multi-step interactive wizard, while the right 4 columns house a persistent contextual summary card displaying requirements, progress, and municipal helpline info.
- **Generous Spatial Rhythm:** Layout sections maintain expansive vertical clearance (`space-xl` / 40px) to prevent visual density. Elements within cards adhere to an 8px modular spacing baseline.
- **Strict Touch Target Rule:** All tap and click affordances (inputs, checkboxes, file selectors, buttons, pagination items) must satisfy a minimum boundary box of 48×48px.

## Elevation & Depth

### Ambient Surface Elevation
Visual depth in this system does not rely on heavy directional drop-shadows or dark skeuomorphic borders. Instead, surfaces achieve spatial separation via subtle ambient shadows softly tinted with the foundational neutral palette.

- **Level 0 (Canvas Base):** Flat `#F8FAFC` foundation. Non-elevated.
- **Level 1 (Interactive Cards & Content Blocks):** 
  `box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.04);`
  Paired with a hairline neutral border (`border: 1px solid #E2E8F0`) to ensure crisp perimeter distinction on lower-contrast screens.
- **Level 2 (Hover States & Elevated Summary Cards):** 
  `box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.04);`
- **Level 3 (Sticky Action Bars & Modal Overlays):** 
  `box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.04);`
- **Authentic Split Gradient:** Desktop authentication panels utilize an immersive, low-contrast gradient anchored from Deep Teal `#0F766E` moving toward Dark Navy `#0B2545` at a 135-degree angle.

## Shapes

### Contour & Geometry Rules
The design system applies a cohesive, friendly roundedness hierarchy that softens administrative interactions:
- **Cards, Panels, and Containers (`12px` to `16px`):** All major information modules, verification boxes, upload boundaries, and modal dialogues use consistent 12px (`rounded-lg`) or 16px (`rounded-xl`) perimeter radii.
- **Interactive Controls (Inputs, Selectors, Buttons):** Standard interactive elements leverage an 8px to 10px radius, striking a balance between approachable softness and structural neatness.
- **Tags, Status Badges & Quick Filters:** Fully rounded pill shapes (`rounded-full`) represent categorical states, timeline milestones, and dynamic filter toggles (e.g., "Semua", "Bansos", "Kesehatan", "Rehabilitasi").
- **Verification Strips:** Highlighted state containers feature an integrated 4px vertical accent bar on the left perimeter (e.g., amber for "Perbaikan Dokumen", emerald for "Terverifikasi Asli").

## Components

### Buttons
- **Primary Button:** Background in Deep Teal (`#0F766E`), text in Pure White (`#FFFFFF`), minimum height of 48px, horizontal padding of 24px (`space-lg`), medium weight font. Hover shifts to `#0D655E`. Focused states show a 3px ring in `#0F766E` with 20% opacity.
- **Secondary Button:** White surface, border in `#CBD5E1`, text in Dark Navy (`#0B2545`). Provides an unobtrusive option for "Kembali" (Back) or "Simpan Draf" (Save Draft).
- **Destructive / Alert Action:** Background in diluted `#FEE2E2`, border in `#FCA5A5`, text in `#B91C1C`.

### Form Fields & Inputs
- **Base Structure:** Minimum touch target height of 48px, 12px vertical padding, 16px horizontal padding. Border colored in `#CBD5E1` on a white background with a base font size of 16px to prevent iOS auto-zoom.
- **Label Hierarchy:** Labels appear strictly above fields in `#0B2545` with a `label-md` weight, accompanied by explicit visual asterisks for mandatory fields.
- **Help & Error Messaging:** Instructional copy sits directly below the field in 14px Slate (`#64748B`). Active errors shift the border to `#EF4444`, displaying an inline Lucide `AlertCircle` icon alongside actionable Indonesian copy (e.g., "Nomor NIK harus terdiri dari 16 digit angka").

### File Upload Zones (Unggah Berkas)
- **Container Styling:** 12px rounded corner zone with a dashed 2px border in `#CBD5E1`, centered icon, and generous interior padding (32px).
- **Validation Badging:** Directly displays file constraints ("Format PDF atau JPG, maksimal 2 MB"). Successful uploads convert the area into a solid white card with a file name chip, document size, and a clear removal button.

### Progress Stepper & Timelines
- **Desktop Stepper:** Horizontal process bar with clear numeric indicators. Completed steps feature a solid green (`#10B981`) circle with an embedded checkmark icon.
- **Active Step Motion:** The current milestone displays Deep Teal (`#0F766E`) accompanied by a subtle breathing/pulsing border animation to orient the citizen.
- **Upcoming Steps:** Muted Slate (`#94A3B8`) outlined circles with connecting lines in `#E2E8F0`.

### Status Badges (Lencana Status)
- **Terverifikasi / Selesai (Success):** Background `#ECFDF5`, text `#065F46`, paired with Lucide `CheckCircle`.
- **Dalam Proses (Info):** Background `#E0F2FE`, text `#0369A1`, paired with Lucide `Clock`.
- **Perbaikan Diminta (Warning):** Background `#FEF3C7`, text `#92400E`, paired with Lucide `AlertTriangle`.
- **Ditolak (Error):** Background `#FEF2F2`, text `#991B1B`, paired with Lucide `XCircle`.

### Verification & Ticket Tracking Card
- Enclosed white surface with an elevated border and a distinctive 6px colored indicator strip on the leading edge.
- Code identifier rendered in `code-tracking` (monospace 15px) inside a subtle `#F1F5F9` pill container.
- Sensitive citizen identities are masked by default (e.g., `"Siti A****"`, NIK hidden) to safeguard privacy across public lookup terminals.