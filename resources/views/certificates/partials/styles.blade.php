<style>
.cert-sheet {
    --cert-primary: #1e4a8c;
    --cert-accent: #c9a227;
    --cert-paper: #fffef8;
    position: relative;
    width: 100%;
    aspect-ratio: 1.414 / 1;
    background:
        radial-gradient(circle at 50% 45%, rgba(30,74,140,0.05), transparent 55%),
        var(--cert-paper);
    border: 10px solid var(--cert-primary);
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
}
.cert-sheet.cert-portrait { aspect-ratio: 1 / 1.414; }
.cert-ornament {
    position: absolute;
    width: 36%;
    height: 36%;
    pointer-events: none;
    z-index: 1;
}
.cert-ornament-tl {
    top: -8%;
    left: -8%;
    background:
        radial-gradient(circle at 30% 30%, var(--cert-accent) 0 18%, transparent 19%),
        radial-gradient(circle at 55% 40%, color-mix(in srgb, var(--cert-primary) 85%, white) 0 28%, transparent 29%),
        radial-gradient(circle at 40% 60%, var(--cert-primary) 0 35%, transparent 36%);
    border-radius: 0 0 80% 0;
    opacity: 0.42;
}
.cert-ornament-br {
    right: -10%;
    bottom: -10%;
    background:
        radial-gradient(circle at 70% 70%, var(--cert-accent) 0 18%, transparent 19%),
        radial-gradient(circle at 45% 60%, color-mix(in srgb, var(--cert-primary) 85%, white) 0 28%, transparent 29%),
        radial-gradient(circle at 60% 40%, var(--cert-primary) 0 35%, transparent 36%);
    border-radius: 80% 0 0 0;
    opacity: 0.42;
}
.cert-theme-emerald {
    --cert-primary: #047857;
    --cert-accent: #f59e0b;
}
.cert-theme-minimal {
    --cert-primary: #334155;
    --cert-accent: #94a3b8;
}
.cert-theme-minimal .cert-ornament { opacity: 0.22; }
.cert-inner {
    position: relative;
    z-index: 2;
    height: 100%;
    padding: 4% 8% 3%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    text-align: center;
    box-sizing: border-box;
    isolation: isolate;
}
.cert-inner::after {
    content: '';
    position: absolute;
    left: 8%;
    right: 20%;
    bottom: 2%;
    height: 32%;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        color-mix(in srgb, var(--cert-paper) 88%, white) 42%,
        color-mix(in srgb, var(--cert-paper) 98%, white) 100%
    );
    pointer-events: none;
    z-index: 0;
    border-radius: 0 0 0.35rem 0.35rem;
}
.cert-inner::before {
    content: '';
    position: absolute;
    inset: 12px;
    border: 1px solid color-mix(in srgb, var(--cert-accent) 55%, transparent);
    pointer-events: none;
    z-index: 0;
}
.cert-qr {
    position: absolute;
    right: 3.25%;
    bottom: 5.5%;
    z-index: 6;
    width: 4.75rem;
    padding: 0.3rem;
    background: #fff;
    border: 2px solid var(--cert-primary);
    border-radius: 0.45rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
    text-align: center;
    pointer-events: none;
}
.cert-qr img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 0.2rem;
}
.cert-qr-label {
    display: block;
    margin-top: 0.2rem;
    font-size: 0.48rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--cert-primary);
    line-height: 1.2;
}
.cert-seal {
    position: absolute;
    right: 3%;
    bottom: 22%;
    z-index: 5;
    width: 4rem;
    height: 4rem;
    pointer-events: none;
    filter: drop-shadow(0 3px 6px rgba(15, 23, 42, 0.12));
}
.cert-seal svg {
    width: 100%;
    height: 100%;
    display: block;
}
.cert-sheet:has(.cert-qr) .cert-seal {
    right: 9.5%;
    bottom: 24%;
    width: 3.25rem;
    height: 3.25rem;
}
.cert-title {
    position: relative;
    z-index: 3;
    margin: 0;
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(1.8rem, 4.2vw, 3rem);
    font-weight: 700;
    color: #0f172a;
    letter-spacing: 0.01em;
    line-height: 1.15;
}
.cert-subtitle {
    position: relative;
    z-index: 3;
    margin: 1rem 0 0;
    font-size: clamp(0.75rem, 1.4vw, 0.95rem);
    color: #64748b;
}
.cert-student {
    position: relative;
    z-index: 3;
    margin: 0.85rem 0 0;
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(1.6rem, 3.6vw, 2.6rem);
    font-weight: 600;
    color: var(--cert-primary);
    min-width: 55%;
    border-bottom: 1px solid #cbd5e1;
    padding-bottom: 0.25rem;
}
.cert-body {
    position: relative;
    z-index: 3;
    margin: 1.1rem auto 0;
    max-width: 36rem;
    font-size: clamp(0.72rem, 1.25vw, 0.9rem);
    line-height: 1.55;
    color: #64748b;
}
.cert-course {
    position: relative;
    z-index: 3;
    margin: 0.85rem 0 0;
    font-size: clamp(0.9rem, 1.6vw, 1.15rem);
    font-weight: 700;
    color: var(--cert-primary);
}
.cert-signs {
    position: relative;
    z-index: 2;
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 2rem;
    margin-top: auto;
    padding: 0.5rem 4% 0.35rem;
    flex-shrink: 0;
}
.cert-sign {
    flex: 1;
    max-width: 12rem;
    padding: 0.35rem 0.5rem 0.25rem;
    border-radius: 0.4rem;
    background: color-mix(in srgb, var(--cert-paper) 96%, white);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.85) inset;
}
.cert-sign-line {
    height: 1px;
    background: #64748b;
    margin-bottom: 0.45rem;
}
.cert-sign span {
    font-size: clamp(0.65rem, 1.1vw, 0.8rem);
    color: #1e293b;
    font-weight: 600;
}
.cert-title,
.cert-subtitle,
.cert-student,
.cert-body,
.cert-course,
.cert-sign,
.cert-footer {
    will-change: transform;
}
.cert-footer {
    position: relative;
    z-index: 2;
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 0;
    padding-top: 0.35rem;
    flex-shrink: 0;
    font-size: clamp(0.6rem, 1vw, 0.72rem);
    color: #334155;
    text-align: left;
}
.cert-footer-item {
    padding: 0.3rem 0.55rem;
    border-radius: 0.35rem;
    background: color-mix(in srgb, var(--cert-paper) 96%, white);
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.85) inset;
}
.cert-footer-item strong {
    color: #0f172a;
    font-weight: 700;
    word-break: break-all;
}
.cert-placeholder {
    display: inline-block;
    padding: 0.05rem 0.35rem;
    border-radius: 0.35rem;
    background: #e0f2fe;
    color: #0369a1;
    font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
    font-size: 0.85em;
}
@media print {
    .cert-sheet { box-shadow: none; border-width: 8px; }
    .cert-sign,
    .cert-footer-item,
    .cert-inner::after,
    .cert-qr,
    .cert-seal {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
