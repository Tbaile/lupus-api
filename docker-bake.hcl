variable "REGISTRY" {
    default = "docker.io"
}

variable "REPOSITORY" {
    default = "tbaile/lupus-api"
}

variable "TAG" {
    default = "develop"
}

target "base" {
    target = "production"
    context = "."
}

target "app" {
    inherits = ["base"]
    dockerfile = "containers/php/Containerfile"
    cache-from = [
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-app:develop",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-app:develop-cache",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-app:${TAG}",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-app:${TAG}-cache"
    ]
}

target "app-release" {
    inherits = ["app"]
    tags = [
        "${REGISTRY}/${REPOSITORY}-app:${TAG}"
    ]
    cache-to = [
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-app:${TAG}-cache,mode=max"
    ]
    output = ["type=registry"]
}

target "app-develop" {
    inherits = ["app"]
    tags = ["${REGISTRY}/${REPOSITORY}-app:develop"]
    output = ["type=docker"]
}

target "web" {
    inherits = ["base"]
    dockerfile = "containers/nginx/Containerfile"
    cache-from = [
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-web:develop",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-web:develop-cache",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-web:${TAG}",
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-web:${TAG}-cache"
    ]
}

target "web-release" {
    inherits = ["web"]
    tags = [
        "${REGISTRY}/${REPOSITORY}-web:${TAG}"
    ]
    cache-to = [
        "type=registry,ref=${REGISTRY}/${REPOSITORY}-web:${TAG}-cache,mode=max"
    ]
    output = ["type=registry"]
}

target "web-develop" {
    inherits = ["web"]
    tags = ["${REGISTRY}/${REPOSITORY}-web:develop"]
    output = ["type=docker"]
}

target "testing" {
    inherits = ["app-develop"]
    target = "testing"
    output = [""]
}

group "release" {
    targets = ["app-release", "web-release"]
}

group "develop" {
    targets = ["app-develop", "web-develop"]
}

group "default" {
    targets = ["develop"]
}
