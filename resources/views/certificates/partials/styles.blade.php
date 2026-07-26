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
    width: 42%;
    height: 42%;
    pointer-events: none;
    z-index: 1;
}
.cert-ornament-tl {
    top: -6%;
    left: -6%;
    background:
        radial-gradient(circle at 30% 30%, var(--cert-accent) 0 18%, transparent 19%),
        radial-gradient(circle at 55% 40%, color-mix(in srgb, var(--cert-primary) 85%, white) 0 28%, transparent 29%),
        radial-gradient(circle at 40% 60%, var(--cert-primary) 0 35%, transparent 36%);
    border-radius: 0 0 80% 0;
    opacity: 0.95;
}
.cert-ornament-br {
    right: -6%;
    bottom: -6%;
    background:
        radial-gradient(circle at 70% 70%, var(--cert-accent) 0 18%, transparent 19%),
        radial-gradient(circle at 45% 60%, color-mix(in srgb, var(--cert-primary) 85%, white) 0 28%, transparent 29%),
        radial-gradient(circle at 60% 40%, var(--cert-primary) 0 35%, transparent 36%);
    border-radius: 80% 0 0 0;
    opacity: 0.95;
}
.cert-theme-emerald {
    --cert-primary: #047857;
    --cert-accent: #f59e0b;
}
.cert-theme-minimal {
    --cert-primary: #334155;
    --cert-accent: #94a3b8;
}
.cert-theme-minimal .cert-ornament { opacity: 0.35; }
.cert-inner {
    position: relative;
    z-index: 2;
    height: 100%;
    padding: 5% 8%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    box-sizing: border-box;
}
.cert-inner::before {
    content: '';
    position: absolute;
    inset: 12px;
    border: 1px solid color-mix(in srgb, var(--cert-accent) 55%, transparent);
    pointer-events: none;
}
.cert-title {
    margin: 0;
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(1.8rem, 4.2vw, 3rem);
    font-weight: 700;
    color: #0f172a;
    letter-spacing: 0.01em;
    line-height: 1.15;
}
.cert-subtitle {
    margin: 1rem 0 0;
    font-size: clamp(0.75rem, 1.4vw, 0.95rem);
    color: #64748b;
}
.cert-student {
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
    margin: 1.1rem auto 0;
    max-width: 36rem;
    font-size: clamp(0.72rem, 1.25vw, 0.9rem);
    line-height: 1.55;
    color: #64748b;
}
.cert-course {
    margin: 0.85rem 0 0;
    font-size: clamp(0.9rem, 1.6vw, 1.15rem);
    font-weight: 700;
    color: var(--cert-primary);
}
.cert-signs {
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 2rem;
    margin-top: 2rem;
    padding: 0 4%;
}
.cert-sign {
    flex: 1;
    max-width: 12rem;
}
.cert-sign-line {
    height: 1px;
    background: #94a3b8;
    margin-bottom: 0.45rem;
}
.cert-sign span {
    font-size: clamp(0.65rem, 1.1vw, 0.8rem);
    color: #475569;
    font-weight: 600;
}
.cert-footer {
    width: 100%;
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: auto;
    padding-top: 1.25rem;
    font-size: clamp(0.6rem, 1vw, 0.72rem);
    color: #64748b;
    text-align: left;
}
.cert-footer-item strong {
    color: #0f172a;
    font-weight: 600;
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
}
</style>
