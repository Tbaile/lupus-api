variable "TAG" {
    default = "develop"
}

target "app" {
    tags = ["tbaile/lupus-app:${TAG}"]
    dockerfile = "docker/php/Dockerfile"
    context = "."
    platforms = ["linux/amd64", "linux/arm64"]
    cache-from = ["type=registry,ref=tbaile/lupus-app:${TAG}"]
}

target "app-release" {
    inherits = ["app"]
    cache-to = ["type=inline"]
    output = ["type=registry"]
}

target "app-develop" {
    inherits = ["app"]
    platforms = ["linux/amd64"]
    output = ["type=docker"]
}

target "web" {
    tags = ["tbaile/lupus-web:${TAG}"]
    dockerfile = "docker/nginx/Dockerfile"
    context = "."
    platforms = ["linux/amd64", "linux/arm64"]
    cache-from = ["type=registry,ref=tbaile/lupus-web:${TAG}"]
}

target "web-release" {
    inherits = ["web"]
    cache-to = ["type=inline"]
    output = ["type=registry"]
}

target "web-develop" {
    inherits = ["web"]
    platforms = ["linux/amd64"]
    output = ["type=docker"]
}

group "release" {
    targets = ["app-release", "web-release"]
}

group "develop" {
    targets = ["app-develop", "web-develop"]
}

target "test" {
    inherits = ["app"]
    target = "testing"
}

