# Simple Domain Parser
[![Release Phase](https://img.shields.io/badge/Release_Phase-alpha-critical.svg)](#support-level)

A simple parser for taking a text string and parsing it into an object.

This parser uses a simplified version of the public suffix list to determine the root suffix and therefore depending on the domain passed may contain top-level, second-level, ...nth-level in the suffix root.

For example:
`www.example.co.uk` will result in a suffixRoot of `co.uk` and the primaryDomain will be `example`.  The subdomain will contain `www`.

## Support Level
**Alpha:** This project is very new and undertaken only basic testing.  Please be cautious using this in a Production environment!