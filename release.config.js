/**
 * @type {import("semantic-release").GlobalConfig}
 */
module.exports = {
    branches: [
        // Regular releases to the default distribution channel from the branch
        {
            name: "main",
            channel: false,
        },

        // Regular releases to a distribution channel matching the branch name from any
        // existing branch with a name matching a maintenance release range (N.N.x or
        // N.x.x or N.x with N being a number)
        {
            name: "+([0-9])?(.{+([0-9]),x}).x",
        },

        // Regular releases to the next distribution channel from the branch next if it exists
        {
            name: "next",
        },

        // Pre-releases to the beta distribution channel from the branch beta if it exists
        {
            name: "beta",
            prerelease: true,
        },

        // Pre-releases to the alpha distribution channel from the branch alpha if it exists
        {
            name: "alpha",
            prerelease: true,
        },
    ],
    repositoryUrl: "git@github.com:ebalo55/crabby.git",
    plugins: [
        [
            "@semantic-release/commit-analyzer",
            {
                releaseRules: [
                    {
                        type: "(docs|style|chore|refactor|revert)",
                        release: "patch",
                    },
                    {
                        type: "refactor",
                        scope: "core-*",
                        release: "minor",
                    },
                    {
                        scope: "no-release",
                        release: false,
                    },
                ],
            },
        ],
        [
            "@semantic-release/release-notes-generator",
            {
                preset: "angular",
                writerOpts: {
                    commitsSort: [
                        "subject",
                        "scope",
                    ],
                },
            },
        ],
        [
            "@semantic-release/exec",
            {
                "prepareCmd": "cargo build --release --target x86_64-unknown-linux-gnu --target x86_64-pc-windows-gnu",
            }
        ],
        [
            "@semantic-release/github",
            {
                "assets": [
                    {"path": "target/x86_64-unknown-linux-gnu/release/crabby", "label": "Crabby Linux binary"},
                    {"path": "target/x86_64-unknown-linux-gnu/release/crabby.exe", "label": "Crabby Windows binary"},
                ]
            }
        ],
        [
            "semantic-release-cargo",
            {
                "allFeatures": true,
                "check": true,
                "publishArgs": ["--locked --target x86_64-unknown-linux-gnu --target x86_64-pc-windows-gnu"]
            }
        ]
    ],
    preset: "angular",
};