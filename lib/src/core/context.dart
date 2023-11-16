class Context {
  final Context? parent;
  final String? segment;

  const Context._([this.parent, this.segment]);
  const Context.root() : this._();

  Context child(String segment) => Context._(this, segment);

  Iterable<String> get segments sync* {
    if (parent != null) yield* parent!.segments;
    if (segment != null) yield segment!;
  }

  String? get path => segments.isEmpty ? null : segments.join('.');
}
