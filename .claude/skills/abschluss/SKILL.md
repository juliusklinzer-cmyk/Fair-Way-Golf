---
name: abschluss
description: Session-Abschluss für das Fair-Way-Golf-Website-Projekt. Prüf-Agents für die geänderten Bereiche laufen lassen, Arbeitsstand in sinnvollen Commits sichern, zu GitHub hochladen, offene Punkte und Docs nachziehen, Zusammenfassung für Julius schreiben. Verwenden am Ende einer Arbeitssession oder wenn Julius "/abschluss" sagt bzw. um Sicherung oder Zusammenfassung bittet.
---

# Session-Abschluss

Führe diese Schritte in Reihenfolge aus.

1. **Fremde Änderungen einsammeln**: Gibt es ein Remote (`git remote -v`),
   dann `git fetch origin` und prüfen, ob der Branch neue Commits hat. Falls
   ja: erst integrieren (rebase), nie überschreiben.
2. **Prüfen statt glauben**: Für die in dieser Session geänderten Bereiche
   die passenden Prüf-Agents laufen lassen (Tabelle in CLAUDE.md unter
   „Agents"): PHP berührt → `wp-code-pruefer`; Seite oder Komponente gebaut →
   `design-ci-pruefer` und `besucher-tester`; Texte → `text-lektor`;
   Formular oder Drittdienst → `datenschutz-sicherheit-pruefer`. Kritische
   Befunde werden behoben, alles andere landet begründet in
   `docs/offene-punkte.md`.
3. **Lokalen Stand sichern**: `git status` prüfen. Zusammengehörende
   Änderungen als sinnvolle Commits mit deutschen, für Julius verständlichen
   Botschaften committen (er liest die Historie als Projekt-Tagebuch). Keine
   halbfertigen Experimente committen; lieber benennen, was offen bleibt.
   Niemals `wordpress/`-Core, `db/`, Uploads oder `.deploy-creds.txt`
   committen (`.gitignore` prüfen).
4. **Docs pflegen**: Neue Seite, neues Formular, neue Entscheidung oder
   geänderter Ablauf? Dann CLAUDE.md, DESIGN.md oder `docs/` ergänzen.
   Entscheidungen mit Wirkung auf andere Projekte (Marke, Infrastruktur,
   Mail) zusätzlich in `~/projects/firmengolf-universum` dokumentieren und
   dort committen.
5. **Hochladen**: `git push origin <branch>`, sobald ein Remote existiert.
6. **Zusammenfassung für Julius**: 3 bis 6 Sätze, kein Jargon: Was ist heute
   entstanden, was wurde entschieden, was ist der nächste Schritt, und was
   muss er selbst tun (z. B. Design freigeben, Zugangsdaten in
   `.deploy-creds.txt` legen, Texte prüfen).
