<?php 
include_once './views/header.php';
?>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- SheetJS para leer Excel -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <style>
        :root {
            --primary: #c41e3a;
            --primary-dark: #a01830;
            --primary-light: #e8304a;
            --primary-glow: rgba(196, 30, 58, 0.35);

            --accent: #0ea5e9;
            --accent-glow: rgba(14, 165, 233, 0.3);

            --success: #10b981;
            --warning: #f59e0b;

            /* OPTIMIZED: Solid backgrounds instead of glass (no backdrop-filter) */
            --glass-bg: rgba(255, 255, 255, 0.97);
            --glass-bg-solid: #ffffff;
            --glass-border: #e2e8f0;
            --glass-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --glass-shadow-lg: 0 12px 24px rgba(0, 0, 0, 0.12);

            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;

            --radius-sm: 12px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;

            /* OPTIMIZED: Faster transitions */
            --transition-fast: 0.15s ease-out;
            --transition-normal: 0.2s ease-out;
            --transition-slow: 0.3s ease-out;
            --transition-spring: 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow: hidden;
            height: 100vh;
            background: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== FULLSCREEN MAP ===== */
        #fullscreen-map {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .leaflet-container {
            background: linear-gradient(180deg, #dbeafe 0%, #e0f2fe 50%, #f0f9ff 100%) !important;
        }

        /* ===== COUNTRY LABELS ON MAP ===== */
        .country-label {
            background: none !important;
            border: none !important;
            z-index: 1000 !important;
            pointer-events: auto !important;
        }

        .country-label-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease-out;
            pointer-events: auto;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .country-label-content:active {
            transform: scale(0.95);
        }

        .country-label-content .number {
            font-family: 'Inter', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #c41e3a;
            text-shadow:
                -1px -1px 0 #fff,
                1px -1px 0 #fff,
                -1px 1px 0 #fff,
                1px 1px 0 #fff,
                0 0 10px rgba(255,255,255,0.8);
            line-height: 1;
        }

        .country-label-content .name {
            font-family: 'Inter', sans-serif;
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow:
                -1px -1px 0 #fff,
                1px -1px 0 #fff,
                -1px 1px 0 #fff,
                1px 1px 0 #fff;
            margin-top: 2px;
        }

        .country-label.hover .country-label-content,
        .country-label-content:hover {
            transform: scale(1.15);
        }

        .country-label.hover .country-label-content .number,
        .country-label-content:hover .number {
            color: #e8304a;
        }

        /* ===== GLOBAL MARKER ===== */
        .global-marker {
            background: none !important;
            border: none !important;
        }

        .global-badge {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 90px;
            height: 90px;
            background: linear-gradient(145deg, #1e293b, #334155);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 8px 32px rgba(30, 41, 59, 0.4);
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .global-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 40px rgba(30, 41, 59, 0.5);
        }

        .global-badge .globe {
            font-size: 1.8rem;
            line-height: 1;
        }

        .global-badge .count {
            font-family: 'Inter', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0ea5e9;
            line-height: 1;
            margin-top: 2px;
        }

        .global-badge .label {
            font-family: 'Inter', sans-serif;
            font-size: 0.55rem;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== COUNTRY POLYGON STYLES ===== */
        .country-polygon {
            cursor: pointer;
        }

        /* Custom map controls */
        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--glass-shadow) !important;
            border-radius: var(--radius-md) !important;
            overflow: hidden;
        }

        .leaflet-control-zoom a {
            background: var(--glass-bg-solid) !important;
            color: var(--text-primary) !important;
            border: none !important;
            width: 40px !important;
            height: 40px !important;
            line-height: 40px !important;
            font-size: 18px !important;
            transition: var(--transition-fast);
        }

        .leaflet-control-zoom a:hover {
            background: white !important;
            color: var(--primary) !important;
        }

        /* ===== FLOATING HEADER ===== */
        .floating-header {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 28px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 100px;
            box-shadow: var(--glass-shadow);
            will-change: transform;
        }

        .floating-header:hover {
            box-shadow: var(--glass-shadow-lg);
            transform: translateX(-50%) translateY(-2px);
        }

        .floating-header img {
            height: 36px;
            transition: var(--transition-fast);
        }

        .floating-header .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 2px;
        }

        .floating-header .logo-img {
            height: 28px;
            width: auto;
        }

        .floating-header .logo-img + .logo-text {
            display: none;
        }

        .floating-header .divider {
            width: 1px;
            height: 30px;
            background: linear-gradient(180deg, transparent, var(--text-tertiary), transparent);
        }

        .floating-header .stats {
            display: flex;
            gap: 24px;
        }

        .floating-header .stat {
            text-align: center;
        }

        .floating-header .stat-value {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
        }

        .floating-header .stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-tertiary);
        }

        /* ===== FLOATING PANEL (Countries) ===== */
        .floating-panel {
            position: fixed;
            top: 100px;
            right: 20px;
            width: 340px;
            max-height: calc(100vh - 140px);
            z-index: 100;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            contain: layout paint;
        }

        .floating-panel.collapsed {
            width: 60px;
            max-height: 60px;
            border-radius: 30px;
            cursor: pointer;
            background: linear-gradient(145deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 20px var(--primary-glow);
        }

        .floating-panel.collapsed:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 28px var(--primary-glow);
        }

        .floating-panel.collapsed .panel-header {
            background: transparent;
            border: none;
            padding: 14px;
            justify-content: center;
        }

        .floating-panel.collapsed .panel-header h3 {
            display: none;
        }

        .floating-panel.collapsed .panel-content {
            opacity: 0;
            pointer-events: none;
            height: 0;
            padding: 0;
        }

        .floating-panel.collapsed .panel-toggle {
            transform: rotate(180deg);
            color: white;
            width: 32px;
            height: 32px;
        }

        .floating-panel.collapsed .panel-toggle:hover {
            background: rgba(255,255,255,0.2);
            color: white;
        }

        .panel-header {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.5);
        }

        .panel-header h3 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .panel-header h3::before {
            content: '';
            width: 4px;
            height: 18px;
            background: var(--primary);
            border-radius: 2px;
        }

        .panel-toggle {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-fast);
            font-size: 1.1rem;
        }

        .panel-toggle:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text-primary);
        }

        .panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            transition: var(--transition-normal);
        }

        .panel-content::-webkit-scrollbar {
            width: 6px;
        }

        .panel-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .panel-content::-webkit-scrollbar-thumb {
            background: var(--text-tertiary);
            border-radius: 3px;
        }

        /* Country Cards in Panel */
        .country-card {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            margin-bottom: 8px;
            background: white;
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-normal);
            position: relative;
            overflow: hidden;
        }

        .country-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary);
            transform: scaleY(0);
            transition: var(--transition-normal);
        }

        .country-card:hover {
            border-color: rgba(196, 30, 58, 0.2);
            transform: translateX(4px);
            box-shadow: 0 4px 20px rgba(196, 30, 58, 0.1);
        }

        .country-card:hover::before {
            transform: scaleY(1);
        }

        .country-card.global {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
        }

        .country-card.global:hover {
            box-shadow: 0 8px 30px rgba(30, 41, 59, 0.3);
        }

        .country-card.global::before {
            background: var(--accent);
        }

        .country-card .flag {
            font-size: 2rem;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .country-card .info h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: inherit;
            margin-bottom: 2px;
        }

        .country-card .info p {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .country-card.global .info p {
            color: rgba(255,255,255,0.7);
        }

        .country-card .arrow {
            margin-left: auto;
            opacity: 0;
            transform: translateX(-8px);
            transition: var(--transition-normal);
            color: var(--primary);
        }

        .country-card.global .arrow {
            color: white;
        }

        .country-card:hover .arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* ===== REGION STATS (Bottom floating) ===== */
        .region-stats {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
            display: flex;
            gap: 12px;
        }

        .region-stat {
            padding: 16px 28px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 100px;
            box-shadow: var(--glass-shadow);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            will-change: transform;
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }

        .region-stat:hover {
            transform: translateY(-4px);
            box-shadow: var(--glass-shadow-lg);
        }

        .region-stat .icon {
            font-size: 1.5rem;
        }

        .region-stat .data {
            text-align: left;
        }

        .region-stat .value {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
        }

        .region-stat .label {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

        /* ===== MODAL OVERLAY ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-out;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        /* ===== ORG MODAL (Large floating panel) ===== */
        .org-modal {
            width: 94%;
            max-width: 1400px;
            max-height: 92vh;
            background: var(--glass-bg-solid);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow-lg);
            display: flex;
            flex-direction: column;
            transform: scale(0.95) translateY(20px);
            opacity: 0;
            transition: transform 0.25s ease-out, opacity 0.25s ease-out;
            overflow: hidden;
        }

        .modal-overlay.active .org-modal {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .org-modal-header {
            padding: 28px 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .org-modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        }

        .org-modal-header .title {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .org-modal-header .flag {
            font-size: 3rem;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        }

        .org-modal-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .org-modal-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .org-modal-header .stats {
            display: flex;
            gap: 12px;
        }

        .org-modal-header .stat {
            text-align: center;
            padding: 12px 20px;
            background: rgba(255,255,255,0.2);
            border-radius: var(--radius-md);
        }

        .org-modal-header .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .org-modal-header .stat-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .org-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition-fast);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .org-modal-close:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.1);
        }

        .org-modal-body {
            flex: 1;
            overflow: auto;
            padding: 28px 32px;
            min-height: 300px;
        }

        .org-modal-body::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .org-modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .org-modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .org-modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Search in modal */
        .modal-search {
            width: 100%;
            max-width: 400px;
            padding: 14px 20px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 100px;
            font-size: 0.9rem;
            font-family: inherit;
            color: var(--text-primary);
            transition: var(--transition-fast);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 16px center;
            background-size: 20px;
            margin-bottom: 24px;
        }

        .modal-search:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        /* ===== ORGANIGRAMA JERÁRQUICO HORIZONTAL ===== */
        .org-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            min-width: 100%;
        }

        /* Nivel horizontal genérico */
        .org-level {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 20px;
            width: 100%;
            position: relative;
            padding-top: 40px;
        }

        .org-level::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 100px);
            max-width: 90%;
            height: 3px;
            background: var(--primary);
        }

        /* Nodo genérico */
        .org-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            min-width: 200px;
        }

        .org-node::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 40px;
            background: var(--primary);
        }

        /* Nodo raíz - País */
        .tree-root {
            padding: 20px 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: var(--radius-lg);
            text-align: center;
            box-shadow: 0 10px 40px var(--primary-glow);
        }

        .tree-root h3 {
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .tree-root span {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Conector vertical */
        .tree-connector {
            width: 3px;
            height: 40px;
            background: var(--primary);
        }

        .tree-connector.dark {
            background: #334155;
        }

        .tree-connector.blue {
            background: var(--accent);
        }

        .tree-connector.yellow {
            background: var(--warning);
        }

        /* Nodo de Director General */
        .tree-director-general {
            padding: 20px 32px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            border-radius: var(--radius-lg);
            text-align: center;
            box-shadow: 0 8px 32px rgba(30, 41, 59, 0.4);
            min-width: 260px;
        }

        .tree-director-general h3 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tree-director-general .director-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--accent);
        }

        .tree-director-general .director-count {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-top: 4px;
        }

        /* Bandera de nacionalidad */
        .director-flag {
            font-size: 1.1em;
            margin-right: 2px;
        }

        .tree-director-general.clickable {
            cursor: pointer;
        }

        .tree-director-general.clickable:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(30, 41, 59, 0.5);
        }

        .click-hint-dg {
            font-size: 0.65rem;
            opacity: 0.6;
            margin-top: 6px;
        }

        /* Tarjeta de Dirección (CCO, CFO, etc.) */
        .direction-card {
            padding: 16px 20px;
            background: white;
            border: 2px solid var(--primary);
            border-radius: var(--radius-md);
            text-align: center;
            min-width: 220px;
            max-width: 280px;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: all 0.2s ease;
            margin-top: 40px;
        }

        .direction-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px var(--primary-glow);
        }

        .direction-card h4 {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .direction-card .director-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .direction-card .director-name.clickable {
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .direction-card .director-name.clickable:hover {
            background: var(--primary-glow);
            color: var(--primary);
        }

        .direction-card .director-name .view-profile {
            font-size: 0.75rem;
            opacity: 0.7;
        }

        .direction-card h4 {
            cursor: pointer;
        }

        .direction-card .badge {
            display: inline-block;
            padding: 4px 12px;
            background: var(--primary);
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
        }

        .direction-card .badge:hover {
            background: var(--primary-dark);
        }

        /* Tarjeta de Área */
        .area-card {
            padding: 14px 18px;
            background: white;
            border: 2px solid var(--accent);
            border-radius: var(--radius-md);
            text-align: center;
            min-width: 180px;
            max-width: 240px;
            box-shadow: 0 4px 15px var(--accent-glow);
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 40px;
        }

        .area-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        .area-card h4 {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 6px;
            line-height: 1.3;
        }

        .area-card .responsible-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .area-card .badge {
            display: inline-block;
            padding: 3px 10px;
            background: var(--accent);
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 600;
            color: white;
        }

        /* Tarjeta de Departamento */
        .dept-card {
            padding: 12px 16px;
            background: white;
            border: 2px solid var(--warning);
            border-radius: var(--radius-sm);
            text-align: center;
            min-width: 150px;
            max-width: 200px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 40px;
        }

        .dept-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(245, 158, 11, 0.2);
        }

        .dept-card h5 {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--warning);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .dept-card .responsible-name {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .dept-card .count {
            font-size: 0.65rem;
            color: var(--text-tertiary);
        }

        /* Líneas de conexión para niveles */
        .org-level.areas::before {
            background: var(--accent);
        }

        .org-level.depts::before {
            background: var(--warning);
        }

        .org-node.area::before {
            background: var(--accent);
        }

        .org-node.dept::before {
            background: var(--warning);
        }

        /* Contenedor scrollable para niveles con muchos elementos */
        .level-scroll-container {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .level-scroll-container::-webkit-scrollbar {
            height: 6px;
        }

        .level-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .level-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .level-scroll-content {
            display: flex;
            justify-content: center;
            gap: 20px;
            min-width: max-content;
            padding: 0 20px;
        }

        /* ===== WORKERS MODAL ===== */
        .workers-modal {
            width: 94%;
            max-width: 1200px;
            max-height: 92vh;
            background: var(--glass-bg-solid);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow-lg);
            display: flex;
            flex-direction: column;
            transform: scale(0.9) translateY(30px);
            transition: var(--transition-spring);
            overflow: hidden;
        }

        .modal-overlay.active .workers-modal {
            transform: scale(1) translateY(0);
        }

        .workers-modal-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .workers-modal-header .back-btn {
            padding: 10px 20px;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 100px;
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            font-family: inherit;
        }

        .workers-modal-header .back-btn:hover {
            background: rgba(255,255,255,0.25);
        }

        .workers-modal-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .workers-modal-header .count {
            padding: 4px 14px;
            background: var(--primary);
            border-radius: 100px;
            font-size: 0.85rem;
        }

        .workers-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px 28px;
            min-height: 250px;
        }

        .workers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding-bottom: 20px;
        }

        /* ===== WORKER CARD (Click to open modal) ===== */
        .worker-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .worker-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-color: var(--primary);
        }

        .worker-card .name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .worker-card .name .flag {
            font-size: 1.3rem;
        }

        .worker-card .position {
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .worker-card .level {
            color: var(--text-tertiary);
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .worker-card .info-row {
            display: flex;
            gap: 12px;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .worker-card .info-row span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .worker-card .click-hint {
            position: absolute;
            bottom: 12px;
            right: 12px;
            font-size: 0.65rem;
            color: var(--text-tertiary);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .worker-card:hover .click-hint {
            opacity: 1;
        }

        /* ===== EMPLOYEE MODAL (Card style) ===== */
        .employee-modal {
            width: 400px;
            max-width: 95%;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow-lg);
            overflow: hidden;
            transform: scale(0.9) translateY(30px);
            transition: var(--transition-spring);
        }

        .modal-overlay.active .employee-modal {
            transform: scale(1) translateY(0);
        }

        .employee-modal-header {
            padding: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
            position: relative;
        }

        .employee-modal-header .close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .employee-modal-header .close-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.1);
        }

        .employee-modal-header .flag-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            font-size: 1.8rem;
        }

        .employee-modal-header .avatar {
            width: 90px;
            height: 90px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            border: 3px solid rgba(255,255,255,0.2);
        }

        .employee-modal-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .employee-modal-header .position {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .employee-modal-body {
            padding: 24px;
        }

        .employee-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .employee-info-item {
            padding: 14px;
            background: #f8fafc;
            border-radius: var(--radius-sm);
        }

        .employee-info-item.full {
            grid-column: span 2;
        }

        .employee-info-item label {
            display: block;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-tertiary);
            margin-bottom: 4px;
        }

        .employee-info-item span {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .employee-modal-footer {
            padding: 0 24px 24px;
        }

        .employee-level {
            padding: 16px;
            background: #f8fafc;
            border-radius: var(--radius-sm);
            text-align: center;
        }

        .employee-level .badge {
            font-size: 1rem;
            font-weight: 700;
            color: var(--primary);
        }

        .employee-level .text {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-tertiary);
            margin-top: 4px;
        }

        /* ===== CUSTOM POPUP ===== */
        .leaflet-popup-content-wrapper {
            background: var(--glass-bg-solid) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--glass-shadow-lg) !important;
            padding: 0 !important;
        }

        .leaflet-popup-tip {
            background: var(--glass-bg-solid) !important;
        }

        .leaflet-popup-content {
            margin: 20px !important;
            text-align: center;
            font-family: 'Inter', sans-serif;
        }

        .popup-content .flag {
            font-size: 3rem;
            margin-bottom: 8px;
        }

        .popup-content .country {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .popup-content .count {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .popup-content .btn {
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-fast);
            font-family: inherit;
        }

        .popup-content .btn:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        /* ===== LOADING ===== */
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            gap: 16px;
            color: var(--text-secondary);
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===== UPLOAD STATE ===== */
        .upload-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .upload-state .icon {
            font-size: 3rem;
            margin-bottom: 16px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .upload-state h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .upload-state p {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        /* Upload state compacto cuando hay datos cargados */
        .upload-state.compact {
            padding: 20px;
        }

        .upload-state.compact .icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .upload-state.compact h4 {
            font-size: 0.9rem;
        }

        .upload-state.compact p {
            font-size: 0.75rem;
            margin-bottom: 12px;
        }

        .upload-btn {
            padding: 14px 28px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 100px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-normal);
            font-family: inherit;
        }

        .upload-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--primary-glow);
        }

        /* Separador entre upload y lista de países */
        #country-list {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--glass-border);
        }

        #country-list:empty {
            display: none;
        }

        /* Sheet Selector */
        .sheet-selector {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            text-align: center;
        }

        .sheet-selector .icon {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        .sheet-selector h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .sheet-selector p {
            font-size: 0.75rem;
            color: var(--text-tertiary);
            margin-bottom: 20px;
        }

        .sheet-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .sheet-btn {
            padding: 14px 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition-normal);
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sheet-btn:hover {
            border-color: var(--primary);
            background: rgba(196, 30, 58, 0.05);
            transform: translateX(4px);
        }

        .sheet-btn .sheet-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sheet-btn .sheet-icon {
            font-size: 1.2rem;
        }

        .sheet-btn .arrow {
            color: var(--primary);
            opacity: 0;
            transition: var(--transition-normal);
        }

        .sheet-btn:hover .arrow {
            opacity: 1;
        }

        .sheet-btn.all-sheets {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .sheet-btn.all-sheets:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .sheet-btn.all-sheets .arrow {
            color: white;
            opacity: 0.7;
        }

        .sheet-btn.all-sheets:hover .arrow {
            opacity: 1;
        }

        /* Format Help Button */
        .format-help-btn {
            margin-top: 16px;
            padding: 10px 16px;
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition-fast);
            font-family: inherit;
        }

        .format-help-btn:hover {
            color: var(--primary);
        }

        /* Format Modal */
        .format-modal {
            width: 600px;
            max-width: 95%;
            max-height: 90vh;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow-lg);
            overflow: hidden;
            transform: scale(0.9) translateY(30px);
            transition: var(--transition-spring);
        }

        .modal-overlay.active .format-modal {
            transform: scale(1) translateY(0);
        }

        .format-modal-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .format-modal-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .format-modal-header .close-btn {
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .format-modal-header .close-btn:hover {
            background: rgba(255,255,255,0.25);
        }

        .format-modal-body {
            padding: 24px;
            overflow-y: auto;
            max-height: calc(90vh - 80px);
        }

        .format-intro {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .format-section {
            margin-bottom: 20px;
        }

        .format-section h4 {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .format-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .format-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f8fafc;
            border-radius: var(--radius-sm);
            border-left: 3px solid #f59e0b;
        }

        .format-item.required {
            border-left-color: var(--primary);
            background: rgba(196, 30, 58, 0.05);
        }

        .format-item.optional {
            border-left-color: #94a3b8;
        }

        .format-item .col-name {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            white-space: nowrap;
        }

        .format-item .col-desc {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .format-example {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .format-example h4 {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .example-table {
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-sm);
            overflow: hidden;
            font-size: 0.75rem;
        }

        .example-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1px;
            background: #e2e8f0;
        }

        .example-row span {
            padding: 10px 8px;
            background: white;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .example-row.header {
            font-weight: 600;
            color: var(--text-primary);
        }

        .example-row.header span {
            background: #f1f5f9;
        }

        /* ===== ANIMATIONS (OPTIMIZED) ===== */
        .stagger {
            opacity: 0;
            transform: translateY(10px);
            animation: staggerIn 0.25s ease-out forwards;
        }

        @keyframes staggerIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Reduce stagger delay for better performance */
        @media (prefers-reduced-motion: reduce) {
            .stagger {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: fixed;
            bottom: 16px;
            left: 20px;
            z-index: 50;
            font-size: 0.95rem;
            color: var(--text-secondary);
            opacity: 0.85;
            font-weight: 600;
            letter-spacing: 0.5px;
            pointer-events: none;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 18px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .floating-panel {
                width: 300px;
            }

            .region-stats {
                flex-wrap: wrap;
                justify-content: center;
                bottom: 16px;
                width: calc(100% - 32px);
                gap: 8px;
            }

            .region-stat {
                padding: 12px 20px;
            }
        }

        @media (max-width: 768px) {
            .floating-header {
                width: calc(100% - 40px);
                padding: 12px 20px;
                border-radius: var(--radius-lg);
            }

            .floating-header .stats {
                display: none;
            }

            .floating-panel {
                top: auto;
                bottom: 80px;
                right: 16px;
                left: 16px;
                width: auto;
                max-height: 50vh;
            }

            .region-stats {
                display: none;
            }

            .org-modal,
            .workers-modal {
                width: 95%;
                max-height: 90vh;
                border-radius: var(--radius-lg);
            }

            .org-modal-header .stats {
                display: none;
            }

            .tree-level {
                flex-direction: column;
                align-items: center;
            }

            .tree-level::before {
                display: none;
            }
        }
    </style>

<body>
    <!-- Fullscreen Map -->
    <div id="fullscreen-map"></div>

    <!-- Watermark -->
    <div class="watermark">Dpt IT Surexport</div>

    <!-- Floating Header -->
    <header class="floating-header mt-5">
        <img src="https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png" alt="Surexport" class="logo-img" id="logo-img" onerror="this.style.display='none'; document.getElementById('logo-fallback').style.display='block';">
        <div class="logo-text" id="logo-fallback">SUREXPORT</div>
        <div class="divider"></div>
        <div class="stats">
            <div class="stat">
                <div class="stat-value" id="stat-empleados">-</div>
                <div class="stat-label">Empleados</div>
            </div>
            <div class="stat">
                <div class="stat-value" id="stat-paises">-</div>
                <div class="stat-label">Países</div>
            </div>
            <div class="stat">
                <div class="stat-value" id="stat-areas">-</div>
                <div class="stat-label">Áreas</div>
            </div>
        </div>
    </header>

    <!-- Floating Countries Panel -->
    <div class="floating-panel" id="countries-panel" onclick="openPanelIfCollapsed(event)">
        <div class="panel-header">
            <h3>Países</h3>
            <button class="panel-toggle" onclick="togglePanel(event)">◀</button>
        </div>
        <div class="panel-content">
            <div class="upload-state" id="upload-state">
                <div class="icon">📂</div>
                <h4>Cargar datos</h4>
                <p>Selecciona archivo Excel (.xlsx) o JSON</p>
                <label class="upload-btn">
                    Seleccionar archivo
                    <input type="file" accept=".json,.xlsx,.xls" style="display:none" onchange="loadFromFile(event)">
                </label>
                <button class="format-help-btn" onclick="showFormatHelp()">ℹ️ Ver formato requerido</button>
            </div>
            <!-- Selector de hojas Excel -->
            <div class="sheet-selector" id="sheet-selector" style="display:none;">
                <div class="icon">📊</div>
                <h4>Selecciona una hoja</h4>
                <p id="sheet-file-name">archivo.xlsx</p>
                <div class="sheet-buttons" id="sheet-buttons"></div>
            </div>
            <!-- Lista de países (se llenará dinámicamente) -->
            <div id="country-list"></div>
        </div>
    </div>

    <!-- Region Stats (Bottom) -->
    <div class="region-stats">
        <div class="region-stat" onclick="showOrg('GLOBAL')">
            <span class="icon">🌐</span>
            <div class="data">
                <div class="value" id="global-count">-</div>
                <div class="label">Global</div>
            </div>
        </div>
        <div class="region-stat">
            <span class="icon">🌍</span>
            <div class="data">
                <div class="value" id="europe-count">-</div>
                <div class="label">Europa</div>
            </div>
        </div>
        <div class="region-stat">
            <span class="icon">🌎</span>
            <div class="data">
                <div class="value" id="america-count">-</div>
                <div class="label">América</div>
            </div>
        </div>
        <div class="region-stat">
            <span class="icon">🌏</span>
            <div class="data">
                <div class="value" id="asia-count">-</div>
                <div class="label">Asia</div>
            </div>
        </div>
    </div>

    <!-- Modal Overlays -->
    <div class="modal-overlay" id="org-overlay" onclick="closeOrgModal(event)">
        <div class="org-modal" onclick="event.stopPropagation()">
            <div class="org-modal-header">
                <div class="title">
                    <span class="flag" id="org-flag">🌐</span>
                    <div>
                        <h2 id="org-country">País</h2>
                        <p>Estructura organizacional</p>
                    </div>
                </div>
                <div class="stats" id="org-stats"></div>
                <button class="org-modal-close" onclick="closeOrgModal()">×</button>
            </div>
            <div class="org-modal-body">
                <input type="text" class="modal-search" id="search-org" placeholder="Buscar empleado..." oninput="filterOrg()">
                <div class="org-tree" id="org-tree"></div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="workers-overlay" onclick="closeWorkersModal(event)">
        <div class="workers-modal" onclick="event.stopPropagation()">
            <div class="workers-modal-header">
                <button class="back-btn" onclick="goBackFromWorkers()">← Volver</button>
                <h2>
                    <span id="workers-flag">👥</span>
                    <span id="workers-title">Equipo</span>
                    <span class="count" id="workers-count">0</span>
                </h2>
            </div>
            <div class="workers-modal-body">
                <input type="text" class="modal-search" id="search-workers" placeholder="Buscar trabajador..." oninput="filterWorkers()">
                <div class="workers-grid" id="workers-grid"></div>
            </div>
        </div>
    </div>

    <!-- Modal de formato requerido -->
    <div class="modal-overlay" id="format-overlay" onclick="closeFormatHelp(event)">
        <div class="format-modal" onclick="event.stopPropagation()">
            <div class="format-modal-header">
                <h3>📋 Formato del Excel</h3>
                <button class="close-btn" onclick="closeFormatHelp()">×</button>
            </div>
            <div class="format-modal-body">
                <p class="format-intro">El archivo Excel debe tener estas columnas en la primera fila (encabezados):</p>

                <div class="format-section">
                    <h4>🔴 Campos obligatorios</h4>
                    <div class="format-grid">
                        <div class="format-item required">
                            <span class="col-name">PAIS</span>
                            <span class="col-desc">País del empleado (ESPAÑA, PORTUGAL, etc.)</span>
                        </div>
                        <div class="format-item required">
                            <span class="col-name">NOMBRE PERSONAL</span>
                            <span class="col-desc">Nombre completo del empleado</span>
                        </div>
                    </div>
                </div>

                <div class="format-section">
                    <h4>🟡 Campos recomendados</h4>
                    <div class="format-grid">
                        <div class="format-item">
                            <span class="col-name">DIRECCIÓN</span>
                            <span class="col-desc">Dirección organizacional</span>
                        </div>
                        <div class="format-item">
                            <span class="col-name">ÁREA</span>
                            <span class="col-desc">Área de trabajo</span>
                        </div>
                        <div class="format-item">
                            <span class="col-name">DEPARTAMENTO</span>
                            <span class="col-desc">Departamento</span>
                        </div>
                        <div class="format-item">
                            <span class="col-name">PUESTO REVISADO</span>
                            <span class="col-desc">Cargo o puesto</span>
                        </div>
                        <div class="format-item">
                            <span class="col-name">FUNCIÓN/NIVEL</span>
                            <span class="col-desc">Nivel jerárquico</span>
                        </div>
                    </div>
                </div>

                <div class="format-section">
                    <h4>⚪ Campos opcionales</h4>
                    <div class="format-grid">
                        <div class="format-item optional">
                            <span class="col-name">MATRIZ</span>
                            <span class="col-desc">Si empieza con "G" → empleado Global</span>
                        </div>
                        <div class="format-item optional">
                            <span class="col-name">NACIONALIDAD</span>
                            <span class="col-desc">Para mostrar bandera (española, portuguesa...)</span>
                        </div>
                        <div class="format-item optional">
                            <span class="col-name">FECHA INCORPORACIÓN</span>
                            <span class="col-desc">Fecha de entrada</span>
                        </div>
                        <div class="format-item optional">
                            <span class="col-name">RESPONSABLE</span>
                            <span class="col-desc">Nombre del responsable directo</span>
                        </div>
                    </div>
                </div>

                <div class="format-example">
                    <h4>📊 Ejemplo de estructura:</h4>
                    <div class="example-table">
                        <div class="example-row header">
                            <span>PAIS</span>
                            <span>NOMBRE PERSONAL</span>
                            <span>DIRECCIÓN</span>
                            <span>ÁREA</span>
                            <span>...</span>
                        </div>
                        <div class="example-row">
                            <span>ESPAÑA</span>
                            <span>Juan García</span>
                            <span>Comercial</span>
                            <span>Ventas</span>
                            <span>...</span>
                        </div>
                        <div class="example-row">
                            <span>PORTUGAL</span>
                            <span>María Silva</span>
                            <span>Operaciones</span>
                            <span>Logística</span>
                            <span>...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="employee-overlay" onclick="closeEmployeeModal(event)">
        <div class="employee-modal" onclick="event.stopPropagation()">
            <div class="employee-modal-header">
                <button class="close-btn" onclick="closeEmployeeModal()">×</button>
                <div class="flag-badge" id="card-flag">🌐</div>
                <div class="avatar">👤</div>
                <h3 id="card-name">Nombre</h3>
                <div class="position" id="card-position">Puesto</div>
            </div>
            <div class="employee-modal-body">
                <div class="employee-info">
                    <div class="employee-info-item">
                        <label>País</label>
                        <span id="card-pais">-</span>
                    </div>
                    <div class="employee-info-item">
                        <label>Nacionalidad</label>
                        <span id="card-nationality">-</span>
                    </div>
                    <div class="employee-info-item full">
                        <label>Dirección</label>
                        <span id="card-direccion">-</span>
                    </div>
                    <div class="employee-info-item">
                        <label>Área</label>
                        <span id="card-area">-</span>
                    </div>
                    <div class="employee-info-item">
                        <label>Departamento</label>
                        <span id="card-dept">-</span>
                    </div>
                    <div class="employee-info-item">
                        <label>Incorporación</label>
                        <span id="card-fecha">-</span>
                    </div>
                    <div class="employee-info-item">
                        <label>Responsable</label>
                        <span id="card-responsable">-</span>
                    </div>
                </div>
            </div>
            <div class="employee-modal-footer">
                <div class="employee-level">
                    <div class="badge" id="card-nivel">-</div>
                    <div class="text">Función / Nivel</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================
        // STATE & DATA
        // ============================================
        let empleados = [];
        let currentCountry = '';
        let currentWorkers = [];
        let map = null;

        const paisesConfig = {
            'GLOBAL': { flag: '🌐', coords: [25, -35], region: 'global', iso: 'GLOBAL' },
            'ESPAÑA': { flag: '🇪🇸', coords: [40.0, -3.5], region: 'europe', iso: 'ESP' },
            'PORTUGAL': { flag: '🇵🇹', coords: [39.6, -8.0], region: 'europe', iso: 'PRT' },
            'MÉXICO': { flag: '🇲🇽', coords: [24.0, -102.5], region: 'america', iso: 'MEX' },
            'PERÚ': { flag: '🇵🇪', coords: [-10.0, -76.0], region: 'america', iso: 'PER' },
            'CHINA': { flag: '🇨🇳', coords: [35.0, 105.0], region: 'asia', iso: 'CHN' },
            'REINO UNIDO': { flag: '🇬🇧', coords: [54.0, -2.0], region: 'europe', iso: 'GBR' },
            'PAÍSES BAJOS': { flag: '🇳🇱', coords: [52.3, 5.5], region: 'europe', iso: 'NLD' }
        };

        // GeoJSON simplificado de países (contornos correctos)
        const countryGeoJSON = {
            'ESP': {"type":"Feature","properties":{"name":"Spain"},"geometry":{"type":"Polygon","coordinates":[[[-9.39,43.02],[-7.98,43.75],[-6.23,43.57],[-4.52,43.40],[-3.12,43.43],[-1.77,43.31],[-1.43,43.03],[0.40,42.71],[3.17,42.44],[3.32,41.87],[1.73,41.22],[0.71,40.66],[0.10,39.86],[-0.05,39.11],[0.11,38.73],[-0.51,37.83],[-1.64,37.38],[-2.12,36.67],[-4.40,36.71],[-5.34,36.16],[-5.60,36.00],[-6.05,36.19],[-6.95,37.19],[-7.41,37.18],[-7.50,37.53],[-6.93,39.01],[-7.50,39.98],[-7.04,40.18],[-6.86,41.94],[-8.15,42.01],[-8.65,42.13],[-9.03,41.88],[-9.39,43.02]]]}},
            'PRT': {"type":"Feature","properties":{"name":"Portugal"},"geometry":{"type":"Polygon","coordinates":[[[-9.03,41.88],[-8.65,42.13],[-8.15,42.01],[-6.86,41.94],[-7.04,40.18],[-7.50,39.98],[-6.93,39.01],[-7.50,37.53],[-7.41,37.18],[-8.00,37.09],[-8.98,37.03],[-8.93,38.02],[-9.48,38.42],[-9.23,39.41],[-8.90,40.07],[-8.69,40.76],[-8.77,41.18],[-8.82,41.79],[-9.03,41.88]]]}},
            'MEX': {"type":"Feature","properties":{"name":"Mexico"},"geometry":{"type":"Polygon","coordinates":[[[-117.13,32.54],[-114.72,32.72],[-111.07,31.33],[-108.21,31.34],[-106.45,31.77],[-104.92,30.59],[-104.40,29.57],[-103.11,28.97],[-102.31,29.85],[-101.41,29.77],[-100.50,28.66],[-99.11,26.43],[-97.14,25.96],[-97.38,22.89],[-97.77,22.07],[-96.56,19.87],[-95.86,18.73],[-94.43,18.14],[-92.79,18.52],[-91.04,18.87],[-90.07,19.86],[-88.60,21.03],[-87.05,21.54],[-86.81,21.33],[-87.43,19.47],[-87.84,18.26],[-88.04,18.48],[-89.23,18.05],[-90.45,16.07],[-91.74,16.07],[-92.15,14.64],[-92.18,15.32],[-93.36,15.62],[-94.69,16.20],[-95.25,16.13],[-96.05,15.75],[-96.56,15.65],[-97.26,15.92],[-98.01,16.11],[-98.95,16.57],[-99.70,16.71],[-100.83,17.17],[-101.92,17.92],[-103.43,18.33],[-103.91,18.83],[-104.99,19.32],[-105.49,19.95],[-105.73,20.43],[-105.40,20.53],[-105.50,21.38],[-105.27,21.49],[-105.27,22.77],[-106.43,23.52],[-107.90,24.55],[-109.43,25.82],[-110.31,24.51],[-112.07,24.54],[-112.15,26.90],[-113.87,26.90],[-115.05,27.77],[-114.98,29.28],[-115.67,29.76],[-116.06,31.00],[-117.13,32.54]]]}},
            'PER': {"type":"Feature","properties":{"name":"Peru"},"geometry":{"type":"Polygon","coordinates":[[[-81.33,-4.23],[-80.88,-3.49],[-80.18,-3.50],[-80.30,-4.01],[-79.77,-4.48],[-79.34,-4.94],[-78.69,-4.55],[-78.34,-3.42],[-77.86,-2.98],[-76.64,-2.61],[-75.57,-1.53],[-75.23,-0.97],[-75.11,-0.06],[-74.44,-0.53],[-73.66,-1.26],[-73.07,-2.31],[-71.05,-1.73],[-70.09,-2.73],[-70.55,-3.79],[-69.89,-4.30],[-70.79,-4.51],[-71.75,-4.59],[-72.89,-5.27],[-73.22,-6.09],[-73.72,-7.34],[-73.99,-7.52],[-73.57,-8.42],[-72.15,-10.05],[-70.55,-11.01],[-69.53,-10.95],[-68.67,-12.56],[-68.98,-13.00],[-69.39,-14.64],[-69.17,-16.22],[-69.42,-17.62],[-69.89,-18.09],[-70.37,-18.35],[-71.12,-17.65],[-73.44,-16.36],[-75.24,-15.27],[-76.01,-14.65],[-76.42,-13.82],[-77.11,-12.22],[-78.09,-10.38],[-79.04,-8.39],[-79.76,-7.19],[-80.54,-6.54],[-81.25,-6.14],[-81.33,-4.23]]]}},
            'CHN': {"type":"Feature","properties":{"name":"China"},"geometry":{"type":"Polygon","coordinates":[[[134.77,48.39],[133.08,45.15],[131.16,45.34],[130.58,42.81],[129.70,42.44],[128.35,41.56],[127.00,41.42],[124.99,40.00],[122.35,39.40],[121.23,37.00],[122.36,36.83],[122.52,33.37],[121.55,31.26],[121.09,28.31],[119.82,26.84],[117.28,23.62],[114.76,22.67],[111.10,21.40],[108.50,21.65],[106.65,22.86],[105.33,23.35],[102.57,22.50],[100.40,21.56],[99.24,22.12],[98.67,24.92],[97.53,25.74],[97.57,28.51],[98.73,27.51],[100.02,28.90],[101.56,29.48],[103.12,30.79],[105.59,32.69],[107.91,32.12],[108.89,32.72],[110.12,33.14],[112.21,34.38],[115.70,34.58],[119.18,34.91],[119.77,35.12],[118.95,37.90],[120.03,38.49],[119.07,39.30],[117.87,39.15],[116.00,39.70],[115.00,40.50],[114.00,40.70],[112.00,40.50],[111.43,41.20],[111.96,43.30],[113.95,44.30],[116.72,45.76],[119.00,46.60],[121.17,47.80],[127.53,49.80],[129.60,49.27],[131.03,48.82],[134.77,48.39]]]}},
            'GBR': {"type":"Feature","properties":{"name":"United Kingdom"},"geometry":{"type":"MultiPolygon","coordinates":[[[[-5.66,54.55],[-6.20,53.87],[-6.95,54.07],[-7.57,54.06],[-7.37,54.60],[-7.57,55.13],[-6.73,55.17],[-5.66,54.55]]],[[[-3.01,58.64],[-4.07,57.55],[-3.06,57.69],[-1.96,57.68],[-2.22,56.87],[-3.12,56.37],[-2.09,55.91],[-2.00,55.80],[-1.11,54.62],[-0.43,54.46],[0.99,53.32],[1.68,52.74],[1.56,52.10],[1.05,51.81],[1.45,51.29],[0.55,50.77],[-0.79,50.77],[-2.49,50.50],[-2.96,50.70],[-3.62,50.23],[-4.54,50.34],[-5.25,49.96],[-5.66,50.17],[-4.79,51.21],[-5.09,51.81],[-4.15,52.31],[-4.22,52.95],[-4.77,52.84],[-4.22,53.21],[-3.11,53.41],[-3.41,54.44],[-4.03,54.43],[-3.70,54.88],[-5.04,55.06],[-5.29,55.45],[-5.67,55.24],[-5.90,55.52],[-5.39,56.03],[-5.79,56.47],[-6.15,56.79],[-5.74,56.99],[-6.15,57.46],[-5.68,57.89],[-5.62,58.25],[-6.26,58.51],[-6.09,58.12],[-6.52,58.05],[-6.71,57.77],[-6.34,58.64],[-5.06,58.63],[-4.40,58.55],[-3.01,58.64]]]]}},
            'NLD': {"type":"Feature","properties":{"name":"Netherlands"},"geometry":{"type":"Polygon","coordinates":[[[6.91,53.48],[7.09,53.14],[7.07,52.62],[6.69,52.55],[7.07,52.24],[6.74,51.91],[6.01,50.76],[5.61,51.04],[4.97,51.48],[4.05,51.27],[3.36,51.37],[3.40,51.62],[4.29,51.98],[3.86,51.62],[3.58,51.45],[4.29,51.37],[5.04,51.64],[5.62,51.05],[5.97,51.81],[5.85,51.14],[6.16,50.80],[6.01,50.76],[5.61,51.04],[4.97,51.48],[4.32,52.31],[4.72,52.96],[4.59,53.09],[5.30,53.38],[6.07,53.51],[6.91,53.48]]]}}
        };

        const nacionalidadFlags = {
            'española': '🇪🇸', 'portugués': '🇵🇹', 'portuguesa': '🇵🇹', 'francesa': '🇫🇷',
            'mexicana': '🇲🇽', 'marroquí': '🇲🇦', 'italiana': '🇮🇹', 'alemana': '🇩🇪',
            'argentina': '🇦🇷', 'colombiana': '🇨🇴', 'venezolana': '🇻🇪', 'peruana': '🇵🇪',
            'chilena': '🇨🇱', 'ecuatoriana': '🇪🇨', 'boliviana': '🇧🇴', 'cubana': '🇨🇺',
            'dominicana': '🇩🇴', 'uruguaya': '🇺🇾', 'paraguaya': '🇵🇾', 'brasileña': '🇧🇷',
            'rumana': '🇷🇴', 'polaca': '🇵🇱', 'británica': '🇬🇧', 'estadounidense': '🇺🇸',
            'búlgara': '🇧🇬', 'ucraniana': '🇺🇦', 'china': '🇨🇳', 'holandesa': '🇳🇱'
        };

        // ============================================
        // UTILITIES
        // ============================================
        function getPaisEfectivo(empleado) {
            if (empleado.MATRIZ && empleado.MATRIZ.startsWith('G')) return 'GLOBAL';
            return empleado.PAIS;
        }

        function getFlag(pais) {
            return paisesConfig[pais]?.flag || '🏳️';
        }

        function getNationalityFlag(n) {
            return n ? (nacionalidadFlags[n.toLowerCase()] || '🏳️') : '🏳️';
        }

        // Bandera de persona (nacionalidad con fallback a país)
        function getPersonFlag(empleado) {
            if (!empleado) return '';
            if (empleado.NACIONALIDAD && empleado.NACIONALIDAD.trim()) {
                const flag = getNationalityFlag(empleado.NACIONALIDAD);
                if (flag && flag !== '🏳️') return flag;
            }
            if (empleado.PAIS) {
                return getFlag(empleado.PAIS);
            }
            return '';
        }

        // Nombre corto (Nombre Apellido)
        function getNombreCorto(nombreCompleto) {
            if (!nombreCompleto) return '';
            const partes = nombreCompleto.split(',');
            if (partes.length >= 2) {
                const apellidos = partes[0].trim();
                const nombre = partes[1].trim().split(' ')[0];
                return `${nombre} ${apellidos.split(' ')[0]}`;
            }
            return nombreCompleto;
        }

        // Buscar jefe por RESPONSABLE (quien reporta a un jefe específico)
        function findJefePorResponsable(empleadosGrupo, nombreJefeSuperior) {
            if (!nombreJefeSuperior) return null;
            const apellidoJefe = nombreJefeSuperior.split(',')[0].trim().split(' ')[0].toUpperCase();
            return empleadosGrupo.find(e => {
                const responsable = (e.RESPONSABLE || '').toUpperCase();
                return responsable.includes(apellidoJefe);
            });
        }

        // ============================================
        // PANEL TOGGLE
        // ============================================
        function togglePanel(event) {
            if (event) event.stopPropagation();
            document.getElementById('countries-panel').classList.toggle('collapsed');
        }

        function openPanelIfCollapsed(event) {
            const panel = document.getElementById('countries-panel');
            if (panel.classList.contains('collapsed')) {
                panel.classList.remove('collapsed');
            }
        }

        // ============================================
        // DATA LOADING
        // ============================================
        let currentWorkbook = null; // Guardar el workbook para selección de hojas

        function loadFromFile(event) {
            const file = event.target.files[0];
            if (!file) return;

            const fileName = file.name.toLowerCase();
            const isExcel = fileName.endsWith('.xlsx') || fileName.endsWith('.xls');

            if (isExcel) {
                // Procesar archivo Excel
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const data = new Uint8Array(e.target.result);
                        currentWorkbook = XLSX.read(data, { type: 'array' });

                        // Si hay más de una hoja, mostrar selector
                        if (currentWorkbook.SheetNames.length > 1) {
                            showSheetSelector(file.name, currentWorkbook.SheetNames);
                        } else {
                            // Solo una hoja, cargar directamente
                            loadSheet(currentWorkbook.SheetNames[0]);
                        }
                    } catch (error) {
                        console.error('Error procesando Excel:', error);
                        alert('Error: No se pudo procesar el archivo Excel.\n\nAsegúrate de que el archivo tenga los encabezados en la primera fila.');
                    }
                };
                reader.readAsArrayBuffer(file);
            } else {
                // Procesar archivo JSON
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        empleados = JSON.parse(e.target.result);
                        console.log(`JSON cargado: ${empleados.length} registros`);
                        initApp();
                    } catch (error) {
                        alert('Error: El archivo no es un JSON válido');
                    }
                };
                reader.readAsText(file);
            }
        }

        function showSheetSelector(fileName, sheetNames) {
            document.getElementById('upload-state').style.display = 'none';
            document.getElementById('sheet-selector').style.display = 'flex';
            document.getElementById('sheet-file-name').textContent = fileName;

            const buttonsContainer = document.getElementById('sheet-buttons');

            // Botón para cargar todas las hojas
            let html = `
                <button class="sheet-btn all-sheets stagger" style="animation-delay: 0s" onclick="loadAllSheets()">
                    <span class="sheet-name">
                        <span class="sheet-icon">📚</span>
                        Cargar todas las hojas
                    </span>
                    <span class="arrow">→</span>
                </button>
                <div style="text-align: center; color: var(--text-tertiary); font-size: 0.75rem; margin: 8px 0;">o selecciona una:</div>
            `;

            // Botones individuales por hoja
            html += sheetNames.map((name, index) => `
                <button class="sheet-btn stagger" style="animation-delay: ${(index + 1) * 0.05}s" onclick="loadSheet('${name.replace(/'/g, "\\'")}')">
                    <span class="sheet-name">
                        <span class="sheet-icon">📄</span>
                        ${name}
                    </span>
                    <span class="arrow">→</span>
                </button>
            `).join('');

            buttonsContainer.innerHTML = html;
        }

        // Normalizar nombres de columnas para compatibilidad entre hojas
        function normalizeEmployeeData(data) {
            // Mapeo de países (códigos -> nombres completos)
            const countryMappings = {
                'UK': 'REINO UNIDO',
                'GB': 'REINO UNIDO',
                'GBR': 'REINO UNIDO',
                'NL': 'PAÍSES BAJOS',
                'NLD': 'PAÍSES BAJOS',
                'HOLANDA': 'PAÍSES BAJOS',
                'ES': 'ESPAÑA',
                'ESP': 'ESPAÑA',
                'PT': 'PORTUGAL',
                'PRT': 'PORTUGAL',
                'MX': 'MÉXICO',
                'MEX': 'MÉXICO',
                'PE': 'PERÚ',
                'PER': 'PERÚ',
                'CN': 'CHINA',
                'CHN': 'CHINA',
            };

            return data.map(row => {
                const normalized = {};

                // Mapeo de columnas (variantes -> nombre estándar)
                const columnMappings = {
                    // País
                    'PAIS': 'PAIS',
                    'PAÍS': 'PAIS',
                    'pais': 'PAIS',
                    'país': 'PAIS',

                    // Fecha incorporación
                    'FECHA INCORPORACIÓN': 'FECHA INCORPORACIÓN',
                    'FECHA INCORPORACION': 'FECHA INCORPORACIÓN',
                    'FECHA DE INCORPORACIÓN': 'FECHA INCORPORACIÓN',
                    'FECHA DE INCORPORACION': 'FECHA INCORPORACIÓN',

                    // Función/Nivel
                    'FUNCIÓN/NIVEL': 'FUNCIÓN/NIVEL',
                    'FUNCION/NIVEL': 'FUNCIÓN/NIVEL',
                    'NIVEL': 'FUNCIÓN/NIVEL',

                    // Dirección
                    'DIRECCIÓN': 'DIRECCIÓN',
                    'DIRECCION': 'DIRECCIÓN',

                    // Área
                    'ÁREA': 'ÁREA',
                    'AREA': 'ÁREA',
                };

                Object.keys(row).forEach(key => {
                    // Ignorar columnas vacías o null
                    if (!key || key === 'null' || key === 'undefined') return;

                    // Buscar si hay un mapeo para esta columna
                    const normalizedKey = columnMappings[key] || key;
                    let value = row[key];

                    // Normalizar valores de país
                    if (normalizedKey === 'PAIS' && value) {
                        const upperValue = String(value).toUpperCase().trim();
                        value = countryMappings[upperValue] || upperValue;
                    }

                    normalized[normalizedKey] = value;
                });

                return normalized;
            });
        }

        function loadSheet(sheetName) {
            if (!currentWorkbook) return;

            try {
                const worksheet = currentWorkbook.Sheets[sheetName];
                let data = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

                // Normalizar columnas
                empleados = normalizeEmployeeData(data);

                console.log(`Excel cargado: ${empleados.length} registros desde "${sheetName}"`);
                console.log('Columnas:', Object.keys(empleados[0] || {}));

                // Ocultar selector pero mantener botón de carga visible
                document.getElementById('sheet-selector').style.display = 'none';

                // Actualizar el mensaje de carga para indicar que hay datos cargados
                const uploadState = document.getElementById('upload-state');
                uploadState.style.display = 'flex';
                uploadState.classList.add('compact');
                uploadState.querySelector('h4').textContent = '✅ Datos cargados';
                uploadState.querySelector('p').textContent = `${empleados.length} empleados cargados. Puedes cargar otro archivo:`;

                initApp();
            } catch (error) {
                console.error('Error cargando hoja:', error);
                alert(`Error al cargar la hoja "${sheetName}"`);
            }
        }

        function loadAllSheets() {
            if (!currentWorkbook) return;

            try {
                empleados = [];

                // Combinar datos de todas las hojas
                currentWorkbook.SheetNames.forEach(sheetName => {
                    const worksheet = currentWorkbook.Sheets[sheetName];
                    let sheetData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

                    // Normalizar columnas de cada hoja
                    sheetData = normalizeEmployeeData(sheetData);

                    empleados = empleados.concat(sheetData);
                    console.log(`Hoja "${sheetName}": ${sheetData.length} registros`);
                });

                console.log(`Total combinado: ${empleados.length} registros de ${currentWorkbook.SheetNames.length} hojas`);
                console.log('Columnas normalizadas:', Object.keys(empleados[0] || {}));

                // Ocultar selector pero mantener botón de carga visible
                document.getElementById('sheet-selector').style.display = 'none';

                // Actualizar el mensaje de carga para indicar que hay datos cargados
                const uploadState = document.getElementById('upload-state');
                uploadState.style.display = 'flex';
                uploadState.classList.add('compact');
                uploadState.querySelector('h4').textContent = '✅ Datos cargados';
                uploadState.querySelector('p').textContent = `${empleados.length} empleados de ${currentWorkbook.SheetNames.length} hojas. Puedes cargar otro archivo:`;

                initApp();
            } catch (error) {
                console.error('Error cargando hojas:', error);
                alert('Error al cargar las hojas del Excel');
            }
        }

        function loadData() {
            // Esperar a que Leaflet cargue antes de inicializar mapa
            function waitForLeaflet() {
                if (typeof L !== 'undefined') {
                    initMapEmpty();
                    loadJSON();
                } else {
                    setTimeout(waitForLeaflet, 100);
                }
            }
            waitForLeaflet();
        }

        function loadJSON() {
            // Intentar cargar el archivo Excel por defecto
            fetch('./organigrama_trabajadores.xlsx')
                .then(r => r.ok ? r.arrayBuffer() : Promise.reject())
                .then(arrayBuffer => {
                    try {
                        const data = new Uint8Array(arrayBuffer);
                        currentWorkbook = XLSX.read(data, { type: 'array' });

                        // Cargar todas las hojas automáticamente
                        if (currentWorkbook.SheetNames.length > 0) {
                            loadAllSheets();
                            console.log('Archivo Excel cargado automáticamente: organigrama_trabajadores.xlsx (todas las hojas)');
                        }
                    } catch (error) {
                        console.error('Error procesando Excel por defecto:', error);
                        tryLoadJSON();
                    }
                })
                .catch(() => {
                    console.log('No se encontró organigrama_trabajadores.xlsx, intentando con JSON...');
                    tryLoadJSON();
                });
        }

        function tryLoadJSON() {
            fetch('./datos_empresa.json')
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    empleados = data;
                    initApp();
                })
                .catch(() => console.log('Usar carga manual - selecciona un archivo JSON o Excel'));
        }

        function initApp() {
            updateStats();
            renderCountryList();
            initMap();
        }

        function initMapEmpty() {
            if (map) return; // Ya existe

            map = L.map('fullscreen-map', {
                center: [25, 0],
                zoom: 2.5,
                minZoom: 2,
                maxZoom: 8,
                zoomControl: true,
                attributionControl: false
            });

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(map);
        }

        // ============================================
        // MAP
        // ============================================
        let countryLayers = {};
        let countryLabels = {};

        function initMap() {
            if (map) map.remove();

            map = L.map('fullscreen-map', {
                center: [25, 0],
                zoom: 2.5,
                minZoom: 2,
                maxZoom: 8,
                zoomControl: true,
                attributionControl: false
            });

            // Beautiful light map style
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19
            }).addTo(map);

            // Add country polygons with outlines
            const paisesConEmpleados = [...new Set(empleados.map(e => getPaisEfectivo(e)).filter(p => p && paisesConfig[p]))];

            paisesConEmpleados.forEach(pais => {
                const config = paisesConfig[pais];
                const count = empleados.filter(e => getPaisEfectivo(e) === pais).length;
                const isGlobal = pais === 'GLOBAL';

                // Para GLOBAL, usar un marcador especial
                if (isGlobal) {
                    const icon = L.divIcon({
                        className: 'global-marker',
                        html: `
                            <div class="global-badge" onclick="showOrg('GLOBAL')">
                                <span class="globe">🌐</span>
                                <span class="count">${count}</span>
                                <span class="label">Global</span>
                            </div>
                        `,
                        iconSize: [90, 90],
                        iconAnchor: [45, 45]
                    });
                    L.marker([20, -30], { icon }).addTo(map);
                    return;
                }

                const geoData = countryGeoJSON[config.iso];
                if (!geoData) return;

                // Crear polígono del país
                const layer = L.geoJSON(geoData, {
                    style: {
                        color: '#c41e3a',
                        weight: 2,
                        opacity: 0.8,
                        fillColor: '#c41e3a',
                        fillOpacity: 0.15
                    }
                }).addTo(map);

                countryLayers[pais] = layer;

                // Eventos del polígono
                layer.on('mouseover', function(e) {
                    this.setStyle({
                        weight: 3,
                        fillOpacity: 0.3,
                        color: '#e8304a'
                    });
                    if (countryLabels[pais]) {
                        countryLabels[pais].getElement().classList.add('hover');
                    }
                });

                layer.on('mouseout', function(e) {
                    this.setStyle({
                        weight: 2,
                        fillOpacity: 0.15,
                        color: '#c41e3a'
                    });
                    if (countryLabels[pais]) {
                        countryLabels[pais].getElement().classList.remove('hover');
                    }
                });

                layer.on('click', function() {
                    showOrg(pais);
                });

                // Añadir etiqueta con número en el centro del país
                const labelIcon = L.divIcon({
                    className: 'country-label',
                    html: `
                        <div class="country-label-content" data-pais="${pais}">
                            <span class="number">${count}</span>
                            <span class="name">${pais}</span>
                        </div>
                    `,
                    iconSize: [80, 50],
                    iconAnchor: [40, 25]
                });

                const label = L.marker(config.coords, {
                    icon: labelIcon,
                    interactive: true
                }).addTo(map);

                countryLabels[pais] = label;

                label.on('click', function() {
                    showOrg(pais);
                });

                label.on('mouseover', function() {
                    if (countryLayers[pais]) {
                        countryLayers[pais].setStyle({
                            weight: 4,
                            fillOpacity: 0.3,
                            color: '#e8304a'
                        });
                    }
                    this.getElement().classList.add('hover');
                });

                label.on('mouseout', function() {
                    if (countryLayers[pais]) {
                        countryLayers[pais].setStyle({
                            weight: 3,
                            fillOpacity: 0.15,
                            color: '#c41e3a'
                        });
                    }
                    this.getElement().classList.remove('hover');
                });
            });
        }

        // ============================================
        // STATS
        // ============================================
        function updateStats() {
            const paisesEfectivos = [...new Set(empleados.map(e => getPaisEfectivo(e)).filter(p => p))];
            const areas = [...new Set(empleados.map(e => e['ÁREA']).filter(a => a && a.trim()))];

            document.getElementById('stat-empleados').textContent = empleados.length;
            document.getElementById('stat-paises').textContent = paisesEfectivos.length;
            document.getElementById('stat-areas').textContent = areas.length;

            document.getElementById('global-count').textContent = empleados.filter(e => getPaisEfectivo(e) === 'GLOBAL').length;
            document.getElementById('europe-count').textContent = empleados.filter(e => paisesConfig[getPaisEfectivo(e)]?.region === 'europe').length;
            document.getElementById('america-count').textContent = empleados.filter(e => paisesConfig[getPaisEfectivo(e)]?.region === 'america').length;
            document.getElementById('asia-count').textContent = empleados.filter(e => paisesConfig[getPaisEfectivo(e)]?.region === 'asia').length;
        }

        // ============================================
        // COUNTRY LIST
        // ============================================
        function renderCountryList() {
            const paises = [...new Set(empleados.map(e => getPaisEfectivo(e)).filter(p => p && paisesConfig[p]))];
            paises.sort((a, b) => {
                if (a === 'GLOBAL') return -1;
                if (b === 'GLOBAL') return 1;
                return empleados.filter(e => getPaisEfectivo(e) === b).length - empleados.filter(e => getPaisEfectivo(e) === a).length;
            });

            document.getElementById('country-list').innerHTML = paises.map((pais, i) => {
                const config = paisesConfig[pais];
                const count = empleados.filter(e => getPaisEfectivo(e) === pais).length;
                const isGlobal = pais === 'GLOBAL';
                return `
                    <div class="country-card ${isGlobal ? 'global' : ''} stagger" style="animation-delay: ${Math.min(i * 0.02, 0.1)}s" onclick="showOrg('${pais}')">
                        <span class="flag">${config.flag}</span>
                        <div class="info">
                            <h4>${pais}</h4>
                            <p>${count} empleados</p>
                        </div>
                        <span class="arrow">→</span>
                    </div>
                `;
            }).join('');
        }

        // ============================================
        // ORG MODAL
        // ============================================
        function showOrg(pais) {
            currentCountry = pais;
            document.getElementById('org-flag').textContent = getFlag(pais);
            document.getElementById('org-country').textContent = pais;
            document.getElementById('search-org').value = '';
            document.getElementById('org-tree').innerHTML = '<div class="loading"><div class="spinner"></div>Cargando...</div>';
            document.getElementById('org-overlay').classList.add('active');
            document.body.style.overflow = 'hidden';

            if (map) map.closePopup();

            setTimeout(() => renderOrganigrama(pais), 100);
        }

        function closeOrgModal(e) {
            if (!e || e.target.id === 'org-overlay' || e.target.classList.contains('modal-overlay')) {
                document.getElementById('org-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function renderOrganigrama(pais) {
            const paisEmpleados = empleados.filter(e => getPaisEfectivo(e) === pais);
            const dirs = [...new Set(paisEmpleados.map(e => e['DIRECCIÓN']).filter(d => d))];
            const areas = [...new Set(paisEmpleados.map(e => e['ÁREA']).filter(a => a && a.trim()))];

            document.getElementById('org-stats').innerHTML = `
                <div class="stat"><div class="stat-value">${paisEmpleados.length}</div><div class="stat-label">Empleados</div></div>
                <div class="stat"><div class="stat-value">${dirs.length}</div><div class="stat-label">Direcciones</div></div>
                <div class="stat"><div class="stat-value">${areas.length}</div><div class="stat-label">Áreas</div></div>
            `;

            // Buscar Director General - por PUESTO REVISADO (sin /) o FUNCIÓN/NIVEL
            const directorGeneral = paisEmpleados.find(e => {
                const puesto = (e['PUESTO REVISADO'] || '').toUpperCase();
                return (puesto === 'DIRECTOR GENERAL' ||
                       (puesto.includes('DIRECTOR GENERAL') && !puesto.includes('/'))) ||
                       (e['FUNCIÓN/NIVEL'] === 'DIRECCIÓN GENERAL' &&
                        (!e.RESPONSABLE || e.RESPONSABLE === '/' || e.RESPONSABLE === ''));
            });

            const nombreDG = directorGeneral ? directorGeneral['NOMBRE PERSONAL'] : '';
            console.log('País:', pais, '| DG:', nombreDG);

            // Agrupar por dirección
            const porDir = {};
            paisEmpleados.forEach(e => {
                const dir = e['DIRECCIÓN'] || 'SIN DIRECCIÓN';
                if (!porDir[dir]) porDir[dir] = [];
                porDir[dir].push(e);
            });

            // Crear mapa de directores para cada dirección (por RESPONSABLE = DG)
            const mapaDirectores = {};
            Object.keys(porDir).forEach(dir => {
                if (dir !== 'DIRECCIÓN GENERAL' && dir !== 'SIN DIRECCIÓN') {
                    const director = findJefePorResponsable(porDir[dir], nombreDG);
                    if (director) {
                        mapaDirectores[dir] = director;
                        console.log(`Director de ${dir}:`, director['NOMBRE PERSONAL']);
                    }
                }
            });

            // === CONSTRUIR ORGANIGRAMA POR NIVELES ===
            let html = '';

            // NIVEL 1: País
            html += `
                <div class="tree-root">
                    <h3>${getFlag(pais)} ${pais}</h3>
                    <span>${paisEmpleados.length} empleados</span>
                </div>
                <div class="tree-connector"></div>
            `;

            // NIVEL 2: Dirección General (clickable si existe director)
            const dgNombre = directorGeneral ? getNombreCorto(directorGeneral['NOMBRE PERSONAL']) : 'Sin asignar';
            const dgFlag = directorGeneral ? getPersonFlag(directorGeneral) : '';
            const dgData = directorGeneral ? JSON.stringify(directorGeneral).replace(/'/g, "\\'").replace(/"/g, '&quot;') : '';

            html += `
                <div class="tree-director-general ${directorGeneral ? 'clickable' : ''}"
                     ${directorGeneral ? `onclick="showEmployee(${dgData})"` : ''}>
                    <h3>Dirección General</h3>
                    <div class="director-name">${dgFlag ? `<span class="director-flag">${dgFlag}</span> ` : ''}${dgNombre}</div>
                    ${directorGeneral ? '<div class="click-hint-dg">Click para ver perfil</div>' : ''}
                </div>
                <div class="tree-connector dark"></div>
            `;

            // NIVEL 3: Direcciones principales (horizontal)
            const direcciones = Object.keys(porDir)
                .filter(d => d !== 'DIRECCIÓN GENERAL' && d !== 'SIN DIRECCIÓN')
                .sort();

            if (direcciones.length > 0) {
                html += `<div class="level-scroll-container"><div class="org-level"><div class="level-scroll-content">`;

                direcciones.forEach(dir => {
                    const directorDeDir = mapaDirectores[dir];
                    const directorNombre = directorDeDir ? getNombreCorto(directorDeDir['NOMBRE PERSONAL']) : '';
                    const directorFlag = directorDeDir ? getPersonFlag(directorDeDir) : '';
                    const empCount = porDir[dir].length;
                    const dirData = directorDeDir ? JSON.stringify(directorDeDir).replace(/'/g, "\\'").replace(/"/g, '&quot;') : '';

                    html += `
                        <div class="org-node">
                            <div class="direction-card">
                                <h4 onclick="showAreasDeDir('${pais}', '${dir.replace(/'/g, "\\'")}')">${dir}</h4>
                                ${directorNombre ? `
                                <div class="director-name clickable"
                                     onclick="event.stopPropagation(); showEmployee(${dirData})">
                                    ${directorFlag ? `<span class="director-flag">${directorFlag}</span> ` : ''}${directorNombre}
                                    <span class="view-profile">👤</span>
                                </div>` : ''}
                                <span class="badge" onclick="showAreasDeDir('${pais}', '${dir.replace(/'/g, "\\'")}')">${empCount} empleados →</span>
                            </div>
                        </div>
                    `;
                });

                html += `</div></div></div>`;
            }

            document.getElementById('org-tree').innerHTML = html;
        }

        // Mostrar áreas de una dirección específica
        function showAreasDeDir(pais, direccion) {
            const paisEmpleados = empleados.filter(e => getPaisEfectivo(e) === pais && e['DIRECCIÓN'] === direccion);

            // Buscar el director de esta dirección (para saber quién es el jefe de las áreas)
            const directorDir = paisEmpleados.find(e => {
                const responsable = (e.RESPONSABLE || '').toUpperCase();
                // El director de la dirección es quien reporta al DG
                return responsable.includes('MORALES') || responsable.includes('MORANT');
            });
            const nombreDirectorDir = directorDir ? directorDir['NOMBRE PERSONAL'] : '';

            // Agrupar por área
            const porArea = {};
            paisEmpleados.forEach(e => {
                const area = e['ÁREA'] && e['ÁREA'].trim() ? e['ÁREA'] : 'SIN ÁREA';
                if (!porArea[area]) porArea[area] = [];
                porArea[area].push(e);
            });

            let html = `
                <div style="margin-bottom: 20px;">
                    <button onclick="renderOrganigrama('${pais}')" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        ← Volver al organigrama
                    </button>
                </div>
                <div class="tree-root" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark));">
                    <h3>${direccion}</h3>
                    <span>${paisEmpleados.length} empleados</span>
                </div>
                <div class="tree-connector"></div>
            `;

            // Nivel de Áreas - buscar jefe de cada área (quien reporta al director de la dirección)
            const areasOrdenadas = Object.keys(porArea).sort();
            if (areasOrdenadas.length > 0) {
                html += `<div class="level-scroll-container"><div class="org-level areas"><div class="level-scroll-content">`;

                areasOrdenadas.forEach(area => {
                    const empArea = porArea[area];
                    const responsable = findJefePorResponsable(empArea, nombreDirectorDir);
                    const responsableNombre = responsable ? getNombreCorto(responsable['NOMBRE PERSONAL']) : '';
                    const responsableFlag = responsable ? getPersonFlag(responsable) : '';

                    html += `
                        <div class="org-node area">
                            <div class="area-card" onclick="showDeptosDeArea('${pais}', '${direccion.replace(/'/g, "\\'")}', '${area.replace(/'/g, "\\'")}')">
                                <h4>${area}</h4>
                                ${responsableNombre ? `<div class="responsible-name">${responsableFlag ? `<span class="director-flag">${responsableFlag}</span> ` : ''}${responsableNombre}</div>` : ''}
                                <span class="badge">${empArea.length}</span>
                            </div>
                        </div>
                    `;
                });

                html += `</div></div></div>`;
            }

            document.getElementById('org-tree').innerHTML = html;
        }

        // Mostrar departamentos de un área específica
        function showDeptosDeArea(pais, direccion, area) {
            const areaEmpleados = empleados.filter(e =>
                getPaisEfectivo(e) === pais &&
                e['DIRECCIÓN'] === direccion &&
                (e['ÁREA'] === area || (area === 'SIN ÁREA' && (!e['ÁREA'] || !e['ÁREA'].trim())))
            );

            // Buscar el jefe de esta área (para saber quién es el jefe de los departamentos)
            const jefeArea = areaEmpleados.find(e => {
                const responsable = (e.RESPONSABLE || '').toUpperCase();
                // Buscar quien reporta al director de la dirección
                return responsable.includes('REDONDO') || responsable.includes('GUANCHE') ||
                       responsable.includes('JIMENEZ') || responsable.includes('PEREZ');
            });
            const nombreJefeArea = jefeArea ? jefeArea['NOMBRE PERSONAL'] : '';

            // Agrupar por departamento
            const porDepto = {};
            areaEmpleados.forEach(e => {
                const dept = e.DEPARTAMENTO || 'SIN DEPARTAMENTO';
                if (!porDepto[dept]) porDepto[dept] = [];
                porDepto[dept].push(e);
            });

            let html = `
                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <button onclick="renderOrganigrama('${pais}')" style="padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        ← Organigrama
                    </button>
                    <button onclick="showAreasDeDir('${pais}', '${direccion.replace(/'/g, "\\'")}')" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        ← ${direccion.substring(0, 20)}...
                    </button>
                </div>
                <div class="tree-root" style="background: linear-gradient(135deg, var(--accent), #0284c7);">
                    <h3>${area}</h3>
                    <span>${areaEmpleados.length} empleados</span>
                </div>
                <div class="tree-connector blue"></div>
            `;

            // Nivel de Departamentos - buscar jefe de cada depto (quien reporta al jefe del área)
            const deptosOrdenados = Object.keys(porDepto).sort();
            if (deptosOrdenados.length > 0) {
                html += `<div class="level-scroll-container"><div class="org-level depts"><div class="level-scroll-content">`;

                deptosOrdenados.forEach(dept => {
                    const empDepto = porDepto[dept];
                    const responsable = findJefePorResponsable(empDepto, nombreJefeArea);
                    const responsableNombre = responsable ? getNombreCorto(responsable['NOMBRE PERSONAL']) : '';
                    const responsableFlag = responsable ? getPersonFlag(responsable) : '';

                    html += `
                        <div class="org-node dept">
                            <div class="dept-card" onclick="showWorkers('${pais}', '${direccion.replace(/'/g, "\\'")}', '${area.replace(/'/g, "\\'")}', '${dept.replace(/'/g, "\\'")}')">
                                <h5>${dept === 'SIN DEPARTAMENTO' ? 'Sin departamento' : dept}</h5>
                                ${responsableNombre ? `<div class="responsible-name">${responsableFlag ? `<span class="director-flag">${responsableFlag}</span> ` : ''}${responsableNombre}</div>` : ''}
                                <div class="count">${empDepto.length} personas</div>
                            </div>
                        </div>
                    `;
                });

                html += `</div></div></div>`;
            }

            document.getElementById('org-tree').innerHTML = html;
        }

        // ============================================
        // WORKERS MODAL
        // ============================================
        function showWorkers(pais, dir, area, dept) {
            currentCountry = pais;
            let filtered = empleados.filter(e => getPaisEfectivo(e) === pais);

            if (dir !== 'SIN DIRECCIÓN') filtered = filtered.filter(e => e['DIRECCIÓN'] === dir);
            else filtered = filtered.filter(e => !e['DIRECCIÓN']);

            if (area !== 'SIN ÁREA') filtered = filtered.filter(e => e['ÁREA'] === area);
            else filtered = filtered.filter(e => !e['ÁREA'] || !e['ÁREA'].trim());

            if (dept !== 'SIN DEPARTAMENTO') filtered = filtered.filter(e => e.DEPARTAMENTO === dept);
            else filtered = filtered.filter(e => !e.DEPARTAMENTO);

            currentWorkers = filtered;

            const deptLabel = dept === 'SIN DEPARTAMENTO' ? 'Sin departamento' : dept;
            document.getElementById('workers-flag').textContent = getFlag(pais);
            document.getElementById('workers-title').textContent = deptLabel;
            document.getElementById('workers-count').textContent = filtered.length;
            document.getElementById('search-workers').value = '';

            document.getElementById('org-overlay').classList.remove('active');
            document.getElementById('workers-overlay').classList.add('active');

            renderWorkers(filtered);
        }

        function closeWorkersModal(e) {
            if (!e || e.target.id === 'workers-overlay') {
                document.getElementById('workers-overlay').classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function goBackFromWorkers() {
            document.getElementById('workers-overlay').classList.remove('active');
            showOrg(currentCountry);
        }

        function renderWorkers(workers) {
            document.getElementById('workers-grid').innerHTML = workers.map((w, i) => {
                const data = JSON.stringify(w).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                return `
                    <div class="worker-card stagger" style="animation-delay: ${Math.min(i * 0.015, 0.1)}s" onclick="showEmployee(${data})">
                        <div class="name">
                            <span class="flag">${getNationalityFlag(w.NACIONALIDAD)}</span>
                            ${w['NOMBRE PERSONAL'] || 'Sin nombre'}
                        </div>
                        <div class="position">${w['PUESTO REVISADO'] || 'Sin puesto'}</div>
                        <div class="level">${w['FUNCIÓN/NIVEL'] || ''}</div>
                        <div class="info-row">
                            <span>📍 ${w['ÁREA'] || '-'}</span>
                            <span>🏢 ${w.DEPARTAMENTO || '-'}</span>
                        </div>
                        <div class="click-hint">Click para ver más →</div>
                    </div>
                `;
            }).join('');
        }

        // ============================================
        // EMPLOYEE MODAL
        // ============================================
        function showEmployee(e) {
            document.getElementById('card-flag').textContent = getNationalityFlag(e.NACIONALIDAD);
            document.getElementById('card-name').textContent = e['NOMBRE PERSONAL'] || 'Sin nombre';
            document.getElementById('card-position').textContent = e['PUESTO REVISADO'] || 'Sin puesto';
            document.getElementById('card-pais').textContent = e.PAIS || '-';
            document.getElementById('card-nationality').textContent = e.NACIONALIDAD || '-';
            document.getElementById('card-direccion').textContent = e['DIRECCIÓN'] || '-';
            document.getElementById('card-area').textContent = e['ÁREA'] || '-';
            document.getElementById('card-dept').textContent = e.DEPARTAMENTO || '-';
            // Manejar fecha que puede ser string o no existir
            let fecha = e['FECHA INCORPORACIÓN'];
            if (fecha && typeof fecha === 'string' && fecha !== 'NaT') {
                document.getElementById('card-fecha').textContent = fecha.substring(0, 10);
            } else {
                document.getElementById('card-fecha').textContent = '-';
            }
            document.getElementById('card-responsable').textContent = e.RESPONSABLE || '-';
            document.getElementById('card-nivel').textContent = e['FUNCIÓN/NIVEL'] || '-';

            document.getElementById('employee-overlay').classList.add('active');
        }

        function closeEmployeeModal(e) {
            if (!e || e.target.id === 'employee-overlay') {
                document.getElementById('employee-overlay').classList.remove('active');
            }
        }

        // ============================================
        // FORMAT HELP MODAL
        // ============================================
        function showFormatHelp() {
            document.getElementById('format-overlay').classList.add('active');
        }

        function closeFormatHelp(e) {
            if (!e || e.target.id === 'format-overlay') {
                document.getElementById('format-overlay').classList.remove('active');
            }
        }

        // ============================================
        // SEARCH
        // ============================================
        function filterOrg() {
            const search = document.getElementById('search-org').value.toLowerCase();
            if (!search) {
                renderOrganigrama(currentCountry);
                return;
            }

            const found = empleados.filter(e =>
                getPaisEfectivo(e) === currentCountry &&
                (e['NOMBRE PERSONAL'] || '').toLowerCase().includes(search)
            );

            if (found.length) {
                currentWorkers = found;
                document.getElementById('org-tree').innerHTML = `
                    <p style="text-align:center; color: var(--text-secondary); margin-bottom: 24px;">
                        ${found.length} resultado${found.length > 1 ? 's' : ''}
                    </p>
                    <div class="workers-grid">${found.map((w, i) => {
                        const data = JSON.stringify(w).replace(/'/g, "\\'").replace(/"/g, '&quot;');
                        return `
                            <div class="worker-card stagger" style="animation-delay: ${Math.min(i * 0.015, 0.1)}s" onclick="showEmployee(${data})">
                                <div class="name">
                                    <span class="flag">${getNationalityFlag(w.NACIONALIDAD)}</span>
                                    ${w['NOMBRE PERSONAL'] || 'Sin nombre'}
                                </div>
                                <div class="position">${w['PUESTO REVISADO'] || 'Sin puesto'}</div>
                                <div class="level">${w['FUNCIÓN/NIVEL'] || ''}</div>
                                <div class="info-row">
                                    <span>📍 ${w['ÁREA'] || '-'}</span>
                                    <span>🏢 ${w.DEPARTAMENTO || '-'}</span>
                                </div>
                                <div class="click-hint">Click para ver más →</div>
                            </div>
                        `;
                    }).join('')}</div>
                `;
            } else {
                document.getElementById('org-tree').innerHTML = '<p style="text-align:center; color: var(--text-tertiary); padding: 60px;">No se encontraron resultados</p>';
            }
        }

        function filterWorkers() {
            const search = document.getElementById('search-workers').value.toLowerCase();
            renderWorkers(currentWorkers.filter(w =>
                (w['NOMBRE PERSONAL'] || '').toLowerCase().includes(search) ||
                (w['PUESTO REVISADO'] || '').toLowerCase().includes(search)
            ));
        }

        // ============================================
        // INIT
        // ============================================
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeFormatHelp();
                closeEmployeeModal();
                closeWorkersModal();
                closeOrgModal();
            }
        });

        document.addEventListener('DOMContentLoaded', loadData);
    </script>
</body>
</html>



<?php 
include_once './views/footer.php';
?>