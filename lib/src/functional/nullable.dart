import '../core/context.dart';
import '../core/ovo.dart';

/// Extension method to make an [OvO] object nullable.
/// Returns a new [OvO] object with nullable type.
extension Nullable<T> on OvO<T> {
  /// Returns an instance of [OvO] that wraps the current value with nullable
  /// type.
  ///
  /// The current value is of type [T] and the returned instance is of type
  /// `OvO<T?>`.
  ///
  /// Example:
  /// ```dart
  /// final schema = OvO<int>().nullable();
  ///
  /// await schema.parse(null); // null
  /// await schema.parse(1); // 1
  /// await schema.parse('1'); // throws OvOException
  /// ```
  OvO<T?> nullable() => _Nullable(this);
}

class _Nullable<T> implements OvO<T?> {
  final OvO<T> parent;

  const _Nullable(this.parent);

  @override
  Future<T?> handle(Context context, data) async {
    if (data == null) return null;

    return parent.handle(context, data);
  }
}
