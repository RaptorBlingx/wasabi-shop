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
        'short_description' => '<p>OVOS-based digital assistant skill for manufacturing energy management, ISO 50001 context, machine status, anomalies, forecasts, KPIs, and action-plan queries.</p>',
        'description' => '<p><strong>HumanEnerDIA</strong> is an Open Voice OS skill for industrial energy management. It connects to the HumanEnerDIA/EnMS backend so operators can ask natural-language questions about factory energy performance, machine status, anomalies, forecasts, KPIs, and ISO 50001 action-plan context.</p>'
            . '<p><strong>Requirements:</strong> Docker, an OVOS-compatible runtime, and a reachable HumanEnerDIA/EnMS API endpoint. The optional Qwen GGUF model is distributed separately and is not included in this download.</p>'
            . '<p><strong>Installation summary:</strong> extract the ZIP, set <code>ENMS_API_URL</code>, install the skill with <code>python3 -m pip install -e .</code>, start the OVOS bridge, then run the smoke query: <code>what is the power of compressor one</code>.</p>'
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
        'description' => '<p><strong>HumanEnerDIA Full Stack</strong> is a guided deployment bundle for the full industrial energy management environment. It includes the HumanEnerDIA backend stack together with the embedded OVOS runtime and skill for natural-language operational queries.</p>'
            . '<p><strong>Includes:</strong> portal, analytics service, PostgreSQL/TimescaleDB initialization, Grafana dashboards, MQTT and Node-RED pipeline, authentication service, simulator, chatbot components, and an <code>ovos-stack/</code> directory with the OVOS runtime and skill source.</p>'
            . '<p><strong>Requirements:</strong> Linux server, Docker Engine, Docker Compose, buyer-provided secrets in <code>.env</code>, and sufficient RAM and disk for a multi-service deployment.</p>'
            . '<p><strong>Installation summary:</strong> extract the archive, copy <code>.env.example</code> to <code>.env</code>, fill required values, run <code>./setup.sh</code>, verify portal and health endpoints, then run a smoke query through the OVOS bridge.</p>'
            . '<p><strong>License/IPR:</strong> the HumanEnerDIA backend/full-stack repository is distributed under the MIT License. The bundled OVOS component remains <code>Apache-2.0 OR GPL-3.0-or-later</code>. Third-party services keep their upstream licenses.</p>'
            . '<p><strong>Known limitation:</strong> this artifact is a guided deployment bundle for evaluation and integration. Production hardening still requires buyer-specific secrets, DNS, TLS, backups, and infrastructure review.</p>',
        'meta_description' => 'HumanEnerDIA full stack deployment bundle with OVOS runtime for WASABI White Label Shop distribution.',
        'available_now' => 'Available as full stack digital download',
        'cover_variant' => 'full-stack',
        'cover_heading' => 'Full Stack',
        'cover_footer' => 'Portal, analytics, automation, and embedded OVOS runtime',
        'cover_badge' => 'WASABI digital download',
    ],
];
