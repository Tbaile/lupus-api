variable "TAG" {
    default = "develop"
}

target "app" {
    dockerfile = "containers/Containerfile"
    context = "."
}

target "app-arm64" {
    inherits = ["app"]
    tags = ["docker.io/tbaile/lupus-app:${TAG}-arm64"]
    platforms = ["linux/arm64"]
    cache-from = ["type=registry,ref=docker.io/tbaile/lupus-app:${TAG}-cache-arm64"]
    target = "testing"
}

target "app-arm64-release" {
    inherits = ["app-arm64"]
    cache-to = ["type=registry,ref=docker.io/tbaile/lupus-app:${TAG}-cache-arm64,mode=max"]
    output = ["type=registry"]
    target = "production"
}

target "app-amd64" {
    inherits = ["app"]
    tags = ["docker.io/tbaile/lupus-app:${TAG}-amd64"]
    platforms = ["linux/amd64"]
    cache-from = ["type=registry,ref=docker.io/tbaile/lupus-app:${TAG}-cache-amd64"]
    target = "testing"
}

target "app-amd64-release" {
    inherits = ["app-amd64"]
    cache-to = ["type=registry,ref=docker.io/tbaile/lupus-app:${TAG}-cache-amd64,mode=max"]
    output = ["type=registry"]
    target = "production"
}

group "app-release" {
    targets = ["app-amd64-release", "app-arm64-release"]
}

target "app-develop" {
    inherits = ["app-amd64"]
    tags = ["docker.io/tbaile/lupus-app:${TAG}"]
    output = ["type=docker"]
    target = "production"
}

target "web" {
    dockerfile = "containers/Containerfile"
    target = "nginx"
    context = "."
}

target "web-arm64" {
    inherits = ["web"]
    tags = ["docker.io/tbaile/lupus-web:${TAG}-arm64"]
    platforms = ["linux/arm64"]
    cache-from = ["type=registry,ref=docker.io/tbaile/lupus-web:${TAG}-cache-arm64"]
}

target "web-arm64-release" {
    inherits = ["web-arm64"]
    cache-to = ["type=registry,ref=docker.io/tbaile/lupus-web:${TAG}-cache-arm64,mode=max"]
    output = ["type=registry"]
}

target "web-amd64" {
    inherits = ["web"]
    tags = ["docker.io/tbaile/lupus-web:${TAG}-amd64"]
    platforms = ["linux/amd64"]
    cache-from = ["type=registry,ref=docker.io/tbaile/lupus-web:${TAG}-cache-amd64"]
}

target "web-amd64-release" {
    inherits = ["web-amd64"]
    cache-to = ["type=registry,ref=docker.io/tbaile/lupus-web:${TAG}-cache-amd64,mode=max"]
    output = ["type=registry"]
}

group "web-release" {
    targets = ["web-amd64-release", "web-arm64-release"]
}

target "web-develop" {
    inherits = ["web-amd64"]
    tags = ["docker.io/tbaile/lupus-web:${TAG}"]
    output = ["type=docker"]
}

group "release" {
    targets = ["app-release", "web-release"]
}

group "develop" {
    targets = ["app-develop", "web-develop"]
}

group "test" {
    targets = ["app-arm64", "app-amd64"]
}

