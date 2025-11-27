# Font-Styling Anleitung

## Noto Serif Display mit Italic Support

Die Schriftart Noto Serif Display ist jetzt mit vollständiger Unterstützung für Italic und alle Weights (100-900) verfügbar.

## Verwendung

### 1. Standard Italic mit CSS
```html
<p style="font-style: italic;">Dieser Text ist kursiv</p>
```

### 2. Mit Utility-Klassen
```html
<!-- Einfach italic -->
<p class="font-italic">Dieser Text ist kursiv</p>

<!-- Italic mit verschiedenen Weights -->
<p class="font-italic font-light">Leicht kursiv (300)</p>
<p class="font-italic font-medium">Medium kursiv (500)</p>
<p class="font-italic font-bold">Fett kursiv (700)</p>
```

### 3. Kombinierte Klassen für Serif + Italic
```html
<!-- Standard Serif Italic -->
<p class="serif-italic">Noto Serif Display Italic</p>

<!-- Serif Italic Light (300) -->
<p class="serif-italic-light">Leichter kursiver Serif-Text</p>

<!-- Serif Italic Medium (500) -->
<p class="serif-italic-medium">Medium kursiver Serif-Text</p>

<!-- Serif Italic Bold (700) -->
<p class="serif-italic-bold">Fetter kursiver Serif-Text</p>
```

### 4. Tailwind-Klassen (weiterhin verfügbar)
```html
<p class="italic">Kursiver Text</p>
<p class="italic font-light">Leicht kursiv</p>
<p class="italic font-bold">Fett kursiv</p>
```

### 5. Mit Inline-Styles
```html
<p style="font-family: var(--aw-font-serif); font-style: italic; font-weight: 300;">
  Individuell gestylter Text
</p>
```

## Verfügbare Font-Weights

- `font-thin` (100)
- `font-extralight` (200)
- `font-light` (300)
- `font-normal` (400)
- `font-medium` (500)
- `font-semibold` (600)
- `font-bold` (700)
- `font-extrabold` (800)
- `font-black` (900)

## Beispiele in Komponenten

### In Astro-Komponenten
```astro
<h2 class="serif-italic-bold">
  Kursive Überschrift
</h2>

<p class="font-italic font-light">
  Ein leicht kursiver Absatz mit Noto Serif Display.
</p>
```

### Mit Tailwind kombinieren
```html
<div class="text-2xl font-italic font-semibold text-green-700">
  Großer, kursiver, grüner Text
</div>
```

## CSS-Variablen

Die folgenden CSS-Variablen stehen zur Verfügung:

- `--aw-font-serif`: Noto Serif Display
- `--aw-font-heading`: Noto Serif Display (für Überschriften)
- `--aw-font-sans`: Quicksand Variable (für Sans-Serif)

## Technische Details

- Die Schriftart nutzt Variable Fonts mit voller Weight-Range (100-900)
- Sowohl `normal` als auch `italic` Styles sind verfügbar
- Optimiert mit `font-display: swap` für bessere Performance
- WOFF2-Format für optimale Kompression
