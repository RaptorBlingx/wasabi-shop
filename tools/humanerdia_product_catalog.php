<?php

return [
    'ovos-skill' => [
        'reference' => 'HUMANERDIA-OVOS-SKILL-1.0.0',
        'artifact_filename' => 'HumanEnerDIA-OVOS-skill-v1.0.0.zip',
        'checksum_filename' => 'HumanEnerDIA-OVOS-skill-v1.0.0.zip.sha256',
        'category_id' => 12,
        'seller_id' => 4,
        'seller_customer_id' => 6,
        'price' => 0,
        'name' => 'HumanEnerDIA OVOS Skill for Industrial Energy Management',
        'short_description' => '<p>Headless OVOS runtime and HumanEnerDIA skill for manufacturing energy management, ISO 50001 context, machine status, anomalies, forecasts, KPIs, and action-plan queries.</p>',
        'description' => '<p><strong>HumanEnerDIA OVOS Skill</strong> provides a natural-language assistant layer for HumanEnerDIA-compatible industrial energy-management systems. It supports operational questions about equipment status, power, energy, anomalies, forecasts, KPIs, ISO 50001 context, and action-plan follow-up.</p>'
            . '<p><strong>Includes:</strong> a headless OVOS Docker runtime, Docker Compose service, REST bridge, HumanEnerDIA OVOS skill source, safe configuration template, release license, and end-user install guide.</p>'
            . '<p><strong>Requirements:</strong> Docker Engine with Compose v2 and a reachable HumanEnerDIA-compatible analytics API endpoint, such as <code>http://&lt;humanerdia-host&gt;:8001/api/v1</code>. The HumanEnerDIA backend is not included in this product; use the full-stack product when you need the backend too. Customers with another EnMS can use this OVOS product by exposing the documented HumanEnerDIA-compatible API through their own adapter/proxy. The optional Qwen GGUF model is distributed separately and is not included in this download.</p>'
            . '<p><strong>Installation summary:</strong> extract the ZIP, run <code>./setup.sh --enms-api-url http://&lt;humanerdia-compatible-host&gt;:8001/api/v1</code>, verify <code>http://localhost:5000/health</code>, then run the smoke query: <code>what is the power of compressor one</code>. Advanced OVOS users can also install only <code>enms-ovos-skill/</code> into an existing OVOS runtime.</p>'
            . '<p><strong>License/IPR:</strong> this WASABI artifact is offered under <code>Apache-2.0 OR GPL-3.0-or-later</code>. Backend services and optional model weights may have separate licenses.</p>'
            . '<p><strong>Known limitation:</strong> fast operational queries are ready for demonstration; the local LLM fallback is slower and should be presented as robustness support for difficult phrasing.</p>',
        'meta_description' => 'HumanEnerDIA OVOS skill bundle for WASABI White Label Shop distribution.',
        'available_now' => 'Available as digital download',
        'cover_variant' => 'ovos-skill',
        'cover_heading' => 'OVOS Skill',
        'cover_footer' => 'Voice assistant bundle for EnMS / ISO 50001 workflows',
        'cover_badge' => 'WASABI digital download',
    ],
    'full-stack' => [
        'reference' => 'HUMANERDIA-FULL-STACK-1.0.0',
        'artifact_filename' => 'HumanEnerDIA-full-stack-v1.0.0.tar.gz',
        'checksum_filename' => 'HumanEnerDIA-full-stack-v1.0.0.tar.gz.sha256',
        'category_id' => 12,
        'seller_id' => 4,
        'seller_customer_id' => 6,
        'price' => 0,
        'name' => 'HumanEnerDIA Full Stack for Industrial Energy Management',
        'short_description' => '<p>Self-hosted HumanEnerDIA deployment bundle with portal, analytics stack, dashboards, automation pipeline, and embedded OVOS runtime for industrial energy management.</p>',
        'description' => '<p><strong>HumanEnerDIA Full Stack</strong> is a zero-touch evaluation bundle for the full industrial energy management environment. It includes the HumanEnerDIA backend stack together with the embedded OVOS runtime and skill for natural-language operational queries.</p>'
            . '<p><strong>Includes:</strong> portal, analytics service, PostgreSQL/TimescaleDB initialization, Grafana dashboards, MQTT and Node-RED pipeline, authentication service, simulator, chatbot components, and an <code>ovos-stack/</code> directory with the OVOS runtime and skill source.</p>'
            . '<p><strong>Requirements:</strong> Linux server, Docker Engine, Docker Compose, network access for image pulls, and sufficient RAM and disk for a multi-service deployment.</p>'
            . '<p><strong>Installation summary:</strong> extract the archive, run <code>./setup.sh</code>, verify portal and health endpoints, then run a smoke query through the OVOS bridge. The setup helper creates <code>.env</code> and generates local first-run secrets; production users should rotate those values and configure DNS/TLS before public exposure.</p>'
            . '<p><strong>License/IPR:</strong> the HumanEnerDIA backend/full-stack repository is distributed under the MIT License. The bundled OVOS component remains <code>Apache-2.0 OR GPL-3.0-or-later</code>. Third-party services keep their upstream licenses.</p>'
            . '<p><strong>Known limitation:</strong> this artifact is a zero-touch evaluation bundle and guided production starting point. Production hardening still requires secret rotation, DNS, TLS, backups, and infrastructure review.</p>',
        'meta_description' => 'HumanEnerDIA full stack deployment bundle with OVOS runtime for WASABI White Label Shop distribution.',
        'available_now' => 'Available as full stack digital download',
        'cover_variant' => 'full-stack',
        'cover_heading' => 'Full Stack',
        'cover_footer' => 'Portal, analytics, automation, and embedded OVOS runtime',
        'cover_badge' => 'WASABI digital download',
    ],
];
