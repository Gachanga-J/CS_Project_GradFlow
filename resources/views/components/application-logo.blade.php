<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Mortarboard base / brim -->
    <polygon points="50,18 95,38 50,58 5,38" fill="currentColor"/>
    <!-- Cap top (slightly darker via opacity) -->
    <polygon points="50,18 95,38 50,58 5,38" fill="currentColor" opacity="0.15"/>
    <!-- Left side of hat body -->
    <path d="M20,44 L20,68 Q50,80 80,68 L80,44 L50,58 Z" fill="currentColor" opacity="0.85"/>
    <!-- Tassel string -->
    <line x1="95" y1="38" x2="95" y2="58" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
    <!-- Tassel bob -->
    <circle cx="95" cy="62" r="4" fill="currentColor"/>
</svg>
